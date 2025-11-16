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
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return WorkedHour::orderBy('date', 'desc')
            ->paginate($perPage);
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

