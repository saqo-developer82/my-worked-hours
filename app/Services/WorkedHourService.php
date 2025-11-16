<?php

namespace App\Services;

use App\Repositories\WorkedHourRepositoryInterface;
use Carbon\Carbon;

class WorkedHourService
{
    public function __construct(
        private WorkedHourRepositoryInterface $repository
    ) {}

    /**
     * Get paginated worked hours.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWorkedHours(int $perPage = 10)
    {
        return $this->repository->getAllPaginated($perPage);
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
                'date' => $data['date'] ?? date('Y-m-d'),
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
        $insertedCount = 0;
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

