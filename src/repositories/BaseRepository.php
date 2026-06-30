<?php

namespace ClinicManagement\Repositories;

use ClinicManagement\Config\ConfigRepository;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var \wpdb
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var ConfigRepository
     */
    protected $config;

    public function __construct(ConfigRepository $config)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->config = $config;
        $this->setTable();
    }

    /**
     * Set the table name from config.
     */
    abstract protected function setTable();

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->get_results($sql) ?: [];
    }

    public function find(int $id)
    {
        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = %d LIMIT 1", $id);
        return $this->db->get_row($sql);
    }

    public function create(array $data)
    {
        $inserted = $this->db->insert($this->table, $data);
        if ($inserted) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update(int $id, array $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete(int $id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Helper to find records by a specific column.
     *
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function getBy(string $column, $value): array
    {
        // Simple sanitization for column name to prevent basic SQLi since column names can't be prepared easily
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        
        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = %s", $value);
        return $this->db->get_results($sql) ?: [];
    }

    /**
     * Get the total count of records.
     *
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->db->get_var($sql);
    }

    /**
     * Get recent records.
     *
     * @param int $limit
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        // Assumes an 'id' or 'created_at' column exists for ordering
        $sql = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d", $limit);
        return $this->db->get_results($sql) ?: [];
    }
}
