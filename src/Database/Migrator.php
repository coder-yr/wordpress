<?php

namespace ClinicManagement\Database;

class Migrator
{
    protected $config;
    
    /**
     * @var \wpdb
     */
    protected $wpdb;

    public function __construct($config)
    {
        $this->config = $config;
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $this->createMigrationsTable();

        $migrations = $this->getPendingMigrations();

        foreach ($migrations as $migrationFile) {
            $this->runMigration($migrationFile);
        }
    }

    protected function createMigrationsTable()
    {
        $table = $this->config->get('database.migrations_table');
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id int(11) NOT NULL AUTO_INCREMENT,
            migration varchar(255) NOT NULL,
            executed_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    protected function getPendingMigrations()
    {
        $table = $this->config->get('database.migrations_table');
        
        $executed = $this->wpdb->get_col("SELECT migration FROM {$table}");
        
        $files = glob(CLINIC_PLUGIN_DIR . 'database/migrations/*.php');
        $pending = [];

        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (!in_array($name, $executed)) {
                $pending[] = $file;
            }
        }

        sort($pending); // Ensure order by filename prefix
        return $pending;
    }

    protected function runMigration($file)
    {
        $name = basename($file, '.php');
        $className = '\\ClinicManagement\\Database\\Migrations\\' . $this->getMigrationClassName($name);

        require_once $file;

        if (class_exists($className)) {
            $migration = new $className($this->config);
            $migration->up();

            // Log execution
            $table = $this->config->get('database.migrations_table');
            $this->wpdb->insert($table, ['migration' => $name]);
        }
    }

    protected function getMigrationClassName($filename)
    {
        // Convert "2023_10_01_create_doctors_table" to "CreateDoctorsTable"
        $parts = explode('_', $filename);
        // Remove date parts
        array_shift($parts);
        array_shift($parts);
        array_shift($parts);

        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }

        return $className;
    }
}
