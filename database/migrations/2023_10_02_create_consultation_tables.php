<?php

namespace ClinicManagement\Database\Migrations;

class CreateConsultationTables
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function up()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Leaves
        $leaves = $this->config->get('database.tables.leaves');
        $sql1 = "CREATE TABLE IF NOT EXISTS {$leaves} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            doctor_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            reason varchar(255) DEFAULT NULL,
            status enum('approved','pending') DEFAULT 'approved',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY doctor_date (doctor_id, date)
        ) $charset_collate;";

        // Consultations
        $consultations = $this->config->get('database.tables.consultations');
        $sql2 = "CREATE TABLE IF NOT EXISTS {$consultations} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            appointment_id bigint(20) unsigned NOT NULL,
            notes text DEFAULT NULL,
            diagnosis text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY appointment_id (appointment_id)
        ) $charset_collate;";

        // Prescriptions
        $prescriptions = $this->config->get('database.tables.prescriptions');
        $sql3 = "CREATE TABLE IF NOT EXISTS {$prescriptions} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            consultation_id bigint(20) unsigned NOT NULL,
            medicine_details text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY consultation_id (consultation_id)
        ) $charset_collate;";

        // Medical Reports
        $reports = $this->config->get('database.tables.reports');
        $sql4 = "CREATE TABLE IF NOT EXISTS {$reports} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) unsigned NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(255) NOT NULL,
            file_type varchar(50) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY patient_id (patient_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
    }
}
