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
     * @param array $validated
     * @return int Number of records inserted
     */
    public function storeWorkedHours(array $validated): int
    {
        $insertedCount = 0;

        // Check if bulk insert is provided
        if (!empty($validated['bulk_insert'])) {
            $insertedCount = $this->processBulkInsert($validated);
        } else {
            // Single insert
            $this->repository->create([
                'task' => $validated['task'],
                'hours' => $validated['hours'] ?? 0,
                'minutes' => $validated['minutes'] ?? 0,
                'date' => $validated['date'] ?? date('Y-m-d'),
            ]);
            $insertedCount = 1;
        }

        return $insertedCount;
    }

    /**
     * Process bulk insert from textarea.
     *
     * @param array $validated
     * @return int Number of records inserted
     */
    private function processBulkInsert(array $validated): int
    {
        $lines = array_filter(array_map('trim', explode("\n", $validated['bulk_insert'])));
        $insertedCount = 0;

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            // Parse the line - could be comma-separated or just task title
            $parts = array_map('trim', explode(',', $line));

            $taskTitle = $parts[0] ?? '';
            $hours = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : ($validated['hours'] ?? 0);
            $minutes = isset($parts[2]) && is_numeric($parts[2]) ? (int)$parts[2] : ($validated['minutes'] ?? 0);
            $date = isset($parts[3]) && !empty($parts[3]) ? $parts[3] : ($validated['date'] ?? date('Y-m-d'));

            // Validate and format date
            $date = $this->validateAndFormatDate($date, $validated['date'] ?? null);

            if (!empty($taskTitle)) {
                $this->repository->create([
                    'task' => $taskTitle,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'date' => $date,
                ]);
                $insertedCount++;
            }
        }

        return $insertedCount;
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

