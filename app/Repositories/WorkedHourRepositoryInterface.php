<?php

namespace App\Repositories;

use App\Models\WorkedHour;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WorkedHourRepositoryInterface
{
    /**
     * Get all worked hours ordered by date descending with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get all worked hours ordered by date descending.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Get worked hours grouped by task within date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getGroupedByTaskInDateRange(string $startDate, string $endDate): array;

    /**
     * Create a new worked hour record.
     *
     * @param array $data
     * @return WorkedHour
     */
    public function create(array $data): WorkedHour;

    /**
     * Insert multiple worked hour records.
     *
     * @param array $data Array of arrays containing worked hour data
     * @return bool
     */
    public function insert(array $data): bool;

    /**
     * Find a worked hour record by ID.
     *
     * @param int $id
     * @return WorkedHour|null
     */
    public function findById(int $id): ?WorkedHour;

    /**
     * Update a worked hour record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a worked hour record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}

