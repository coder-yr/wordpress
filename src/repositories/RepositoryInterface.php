<?php

namespace ClinicManagement\Repositories;

interface RepositoryInterface
{
    /**
     * Get all records.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Find a record by its primary key.
     *
     * @param int $id
     * @return object|null
     */
    public function find(int $id);

    /**
     * Create a new record.
     *
     * @param array $data
     * @return int|false The inserted ID or false on failure.
     */
    public function create(array $data);

    /**
     * Update an existing record.
     *
     * @param int $id
     * @param array $data
     * @return int|false The number of rows updated or false on failure.
     */
    public function update(int $id, array $data);

    /**
     * Delete a record by primary key.
     *
     * @param int $id
     * @return int|false The number of rows deleted or false on failure.
     */
    public function delete(int $id);
}
