<?php

namespace App\Repositories;

use App\Models\WorkedHour;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WorkedHourRepository implements WorkedHourRepositoryInterface
{
    /**
     * Get all worked hours ordered by date descending with pagination.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = WorkedHour::query();

        // Filter by task (case-insensitive LIKE)
        if (!empty($filters['task'])) {
            $query->whereRaw('LOWER(task) LIKE ?', ['%' . strtolower($filters['task']) . '%']);
        }

        // Filter by date interval (takes precedence over single date)
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['date'])) {
            // Filter by single date
            $query->whereDate('date', $filters['date']);
        }

        return $query->orderBy('date', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all worked hours ordered by date descending.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return WorkedHour::orderBy('date', 'desc')->get();
    }

    /**
     * Get worked hours grouped by task within date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getGroupedByTaskInDateRange(string $startDate, string $endDate): array
    {
        return WorkedHour::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('task, SUM(hours) as total_hours, SUM(minutes) as total_minutes')
            ->groupBy('task')
            ->orderBy('task')
            ->get()
            ->map(function ($item) {
                return [
                    'task' => $item->task,
                    'total_hours' => (int)$item->total_hours,
                    'total_minutes' => (int)$item->total_minutes,
                ];
            })
            ->toArray();
    }

    /**
     * Get total hours and minutes within date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array ['total_hours' => int, 'total_minutes' => int]
     */
    public function getTotalHoursInDateRange(string $startDate, string $endDate): array
    {
        $result = WorkedHour::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(hours) as total_hours, SUM(minutes) as total_minutes')
            ->first();

        return [
            'total_hours' => (int)($result->total_hours ?? 0),
            'total_minutes' => (int)($result->total_minutes ?? 0),
        ];
    }

    /**
     * Get total hours and minutes with filters applied.
     *
     * @param array $filters
     * @return array ['total_hours' => int, 'total_minutes' => int]
     */
    public function getTotalHoursWithFilters(array $filters = []): array
    {
        $query = WorkedHour::query();

        // Filter by task (case-insensitive LIKE)
        if (!empty($filters['task'])) {
            $query->whereRaw('LOWER(task) LIKE ?', ['%' . strtolower($filters['task']) . '%']);
        }

        // Filter by date interval (takes precedence over single date)
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['date'])) {
            // Filter by single date
            $query->whereDate('date', $filters['date']);
        }

        $result = $query->selectRaw('SUM(hours) as total_hours, SUM(minutes) as total_minutes')
            ->first();

        return [
            'total_hours' => (int)($result->total_hours ?? 0),
            'total_minutes' => (int)($result->total_minutes ?? 0),
        ];
    }

    /**
     * Create a new worked hour record.
     *
     * @param array $data
     * @return WorkedHour
     */
    public function create(array $data): WorkedHour
    {
        return WorkedHour::create($data);
    }

    /**
     * Insert multiple worked hour records.
     *
     * @param array $data Array of arrays containing worked hour data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return WorkedHour::insert($data);
    }

    /**
     * Find a worked hour record by ID.
     *
     * @param int $id
     * @return WorkedHour|null
     */
    public function findById(int $id): ?WorkedHour
    {
        return WorkedHour::find($id);
    }

    /**
     * Update a worked hour record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return WorkedHour::where('id', $id)->update($data) > 0;
    }

    /**
     * Delete a worked hour record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return WorkedHour::where('id', $id)->delete() > 0;
    }
}

