<?php

namespace App\Services;

use App\Repositories\WorkedHourRepositoryInterface;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WorkedHourService
{
    public function __construct(
        private WorkedHourRepositoryInterface $repository
    ) {}

    /**
     * Get paginated worked hours.
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWorkedHours(int $perPage = 10, array $filters = [])
    {
        return $this->repository->getAllPaginated($perPage, $filters);
    }

    /**
     * Get all worked hours for export.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWorkedHours()
    {
        return $this->repository->getAll();
    }

    /**
     * Store worked hours (single or bulk).
     *
     * @param array $data
     * @return int Number of records inserted
     */
    public function storeWorkedHours(array $data): int
    {
        $insertedCount = 0;

        // Check if bulk insert is provided
        if (!empty($data['bulk_insert'])) {
            $insertedCount = $this->processBulkInsert($data);
        }

        if (!empty($data['task'])) {
            // Single insert
            $this->repository->create([
                'task' => $data['task'],
                'hours' => $data['hours'] ?? 0,
                'minutes' => $data['minutes'] ?? 0,
                'date' => !empty($data['date']) ? $data['date'] : date('Y-m-d'),
            ]);
            ++$insertedCount;
        }

        return $insertedCount;
    }

    /**
     * Process bulk insert from textarea.
     *
     * @param array $data
     * @return int Number of records inserted
     */
    private function processBulkInsert(array $data): int
    {
        $lines = array_filter(array_map('trim', explode("\n", $data['bulk_insert'])));
        $dataToInsert = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Expect format: "<task text>\t<time>" or "<task text> <time>"
            // Time examples: "5m", "1h", "1h 50m", "3h:30m"
            // We'll split task and time by finding the final time pattern at end of line.

            // Normalize internal whitespace for robust matching (tabs/spaces)
            $normalized = preg_replace('/\s+/', ' ', $line);
            if ($normalized === null) {
                $normalized = $line; // Fallback if regex fails
            }

            // Match: capture task text (group 1), hours (group 2, optional), minutes (group 3, optional)
            // Allows optional colon or space between hours and minutes.
            $pattern = '/^(.*?)\s+(?:(\d+)\s*h)?(?:\s*:?\s*(\d+)\s*m)?$/i';

            $task = $normalized;
            $hours = 0;
            $minutes = 0;

            if (preg_match($pattern, $normalized, $m)) {
                $task = trim($m[1]);
                if (isset($m[2]) && $m[2] !== '') {
                    $hours = (int) $m[2];
                }
                if (isset($m[3]) && $m[3] !== '') {
                    $minutes = (int) $m[3];
                }
            } else {
                // If it doesn't match, attempt minutes-only at end (e.g., "Task 15m")
                if (preg_match('/^(.*?)\s+(\d+)\s*m$/i', $normalized, $mm)) {
                    $task = trim($mm[1]);
                    $minutes = (int) $mm[2];
                } else {
                    // Leave as-is with zeroed time to avoid data loss
                    $task = $normalized;
                }
            }
    
            $dataToInsert[] = [
                'task' => $task,
                'hours' => $hours,
                'minutes' => $minutes,
                'date' => !empty($data['date']) ? $data['date'] : date('Y-m-d'),
            ];
        }

        $this->repository->insert($dataToInsert);

        return count($dataToInsert);
    }

    /**
     * Get a worked hour record by ID.
     *
     * @param int $id
     * @return \App\Models\WorkedHour|null
     */
    public function getWorkedHourById(int $id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Update a worked hour record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateWorkedHour(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a worked hour record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWorkedHour(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get worked hours grouped by task for export.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getGroupedWorkedHoursForExport(string $startDate, string $endDate): array
    {
        $data = $this->repository->getGroupedByTaskInDateRange($startDate, $endDate);
        
        // Convert excess minutes to hours for each task
        return array_map(function ($item) {
            $hours = $item['total_hours'];
            $minutes = $item['total_minutes'];
            
            // Convert excess minutes to hours
            $hours += intval($minutes / 60);
            $minutes = $minutes % 60;
            
            return [
                'task' => $item['task'],
                'total_hours' => $hours,
                'total_minutes' => $minutes,
            ];
        }, $data);
    }

    /**
     * Generate Excel file for export.
     *
     * @param string $startDate
     * @param string $endDate
     * @param bool $includeHours Whether to include hours/duration in the export
     * @return array ['filePath' => string, 'filename' => string]
     */
    public function generateExportFile(string $startDate, string $endDate, bool $includeHours = true): array
    {
        // Get grouped data
        $groupedData = $this->getGroupedWorkedHoursForExport($startDate, $endDate);

        // Calculate totals (only if including hours)
        $totalHours = 0;
        $totalMinutes = 0;
        if ($includeHours) {
            foreach ($groupedData as $item) {
                $totalHours += $item['total_hours'];
                $totalMinutes += $item['total_minutes'];
            }

            // Convert excess minutes to hours
            $totalHours += intval($totalMinutes / 60);
            $totalMinutes = $totalMinutes % 60;
        }

        // Load template file
        $templatePath = public_path('Report_template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Add data rows starting from row 2
        $row = 2;
        foreach ($groupedData as $item) {
            $sheet->setCellValue('A' . $row, $item['task']);
            if ($includeHours) {
                $sheet->setCellValue('B' . $row, $this->formatDuration($item['total_hours'], $item['total_minutes']));
            }
            $row++;
        }

        // Add total row (only if including hours)
        if ($includeHours) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, $this->formatDuration($totalHours, $totalMinutes));

            // Style total row
            $totalStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D0D0D0'],
                ],
            ];
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($totalStyle);
        }

        // Generate filename
        $filename = 'Report_' . $startDate . '_to_' . $endDate . '.xlsx';
        $filePath = storage_path('app/' . $filename);

        // Write file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return [
            'filePath' => $filePath,
            'filename' => $filename,
        ];
    }

    /**
     * Get total worked hours for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array ['total_hours' => int, 'total_minutes' => int, 'formatted' => string]
     */
    public function getTotalWorkedHoursForDateRange(string $startDate, string $endDate): array
    {
        $totals = $this->repository->getTotalHoursInDateRange($startDate, $endDate);
        
        $totalHours = $totals['total_hours'];
        $totalMinutes = $totals['total_minutes'];
        
        // Convert excess minutes to hours
        $totalHours += intval($totalMinutes / 60);
        $totalMinutes = $totalMinutes % 60;
        
        return [
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutes,
            'formatted' => $this->formatDuration($totalHours, $totalMinutes),
        ];
    }

    /**
     * Get total worked hours with filters applied.
     *
     * @param array $filters
     * @return array ['total_hours' => int, 'total_minutes' => int, 'formatted' => string]
     */
    public function getTotalWorkedHoursWithFilters(array $filters = []): array
    {
        $totals = $this->repository->getTotalHoursWithFilters($filters);
        
        $totalHours = $totals['total_hours'];
        $totalMinutes = $totals['total_minutes'];
        
        // Convert excess minutes to hours
        $totalHours += intval($totalMinutes / 60);
        $totalMinutes = $totalMinutes % 60;
        
        return [
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutes,
            'formatted' => $this->formatDuration($totalHours, $totalMinutes),
        ];
    }

    /**
     * Format duration string.
     *
     * @param int $hours
     * @param int $minutes
     * @return string
     */
    public function formatDuration(int $hours, int $minutes): string
    {
        if ($hours == 0 && $minutes == 0) {
            return '0m';
        }
        
        if ($hours == 0) {
            return $minutes . 'm';
        }
        
        if ($minutes == 0) {
            return $hours . 'h';
        }
        
        return $hours . 'h:' . $minutes . 'm';
    }

    /**
     * Validate and format date string.
     *
     * @param string $date
     * @param string|null $fallbackDate
     * @return string
     */
    private function validateAndFormatDate(string $date, ?string $fallbackDate = null): string
    {
        if (!empty($date)) {
            try {
                $dateObj = Carbon::createFromFormat('Y-m-d', $date);
                return $dateObj->format('Y-m-d');
            } catch (\Exception $e) {
                return $fallbackDate ?? date('Y-m-d');
            }
        }

        return $fallbackDate ?? date('Y-m-d');
    }
}

