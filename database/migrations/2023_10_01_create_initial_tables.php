<?php

namespace ClinicManagement\Database\Migrations;

class CreateInitialTables
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

        // Departments
        $departments = $this->config->get('database.tables.departments');
        $sql1 = "CREATE TABLE IF NOT EXISTS {$departments} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            status tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Doctors
        $doctors = $this->config->get('database.tables.doctors');
        $sql2 = "CREATE TABLE IF NOT EXISTS {$doctors} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            department_id bigint(20) unsigned NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            specialization varchar(255) NOT NULL,
            qualification varchar(255) DEFAULT NULL,
            experience_years int(11) DEFAULT 0,
            consultation_fee decimal(10,2) DEFAULT 0.00,
            bio text DEFAULT NULL,
            status varchar(50) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY department_id (department_id)
        ) $charset_collate;";

        // Patients
        $patients = $this->config->get('database.tables.patients');
        $sql3 = "CREATE TABLE IF NOT EXISTS {$patients} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            date_of_birth date DEFAULT NULL,
            gender enum('male','female','other') DEFAULT NULL,
            blood_group varchar(5) DEFAULT NULL,
            address text DEFAULT NULL,
            emergency_contact varchar(20) DEFAULT NULL,
            medical_history text DEFAULT NULL,
            status varchar(50) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        // Appointments
        $appointments = $this->config->get('database.tables.appointments');
        $sql4 = "CREATE TABLE IF NOT EXISTS {$appointments} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) unsigned NOT NULL,
            doctor_id bigint(20) unsigned NOT NULL,
            department_id bigint(20) unsigned NOT NULL,
            appointment_date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            status enum('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
            reason text DEFAULT NULL,
            meeting_link varchar(255) DEFAULT NULL,
            token_number varchar(50) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY doctor_date (doctor_id, appointment_date),
            KEY patient_id (patient_id)
        ) $charset_collate;";

        // Doctor Availability
        $availability = $this->config->get('database.tables.availability');
        $sql5 = "CREATE TABLE IF NOT EXISTS {$availability} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            doctor_id bigint(20) unsigned NOT NULL,
            day_of_week tinyint(1) NOT NULL COMMENT '0=Sunday, 6=Saturday',
            start_time time NOT NULL,
            end_time time NOT NULL,
            break_start time DEFAULT NULL,
            break_end time DEFAULT NULL,
            slot_duration_mins int(11) DEFAULT 15,
            PRIMARY KEY  (id),
            KEY doctor_id (doctor_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
        dbDelta($sql5);
    }
}
