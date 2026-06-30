<?php
/**
 * Plugin Name: Clinic Management System
 * Plugin URI: https://digirise.com
 * Description: A production-ready Clinic Management System built with a Laravel-style architecture.
 * Version: 1.0.0
 * Author: Digirise
 * Author URI: https://digirise.com
 * Text Domain: clinic-management
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLINIC_PLUGIN_FILE', __FILE__);
define('CLINIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLINIC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require the Composer autoloader
require_once CLINIC_PLUGIN_DIR . 'vendor/autoload.php';

// Boot the application
$app = \ClinicManagement\Bootstrap\Application::getInstance();
$app->boot();

// TEMP: Run migrator for updated schema
add_action('admin_init', function() {
    $executed = get_option('clinic_temp_migrator_ran_v4', false);
    if (!$executed) {
        global $wpdb;
        $doctors = $wpdb->prefix . 'clinic_doctors';
        $patients = $wpdb->prefix . 'clinic_patients';
        
        $doctor_cols = [
            'first_name' => "varchar(255) NOT NULL DEFAULT ''",
            'last_name' => "varchar(255) NOT NULL DEFAULT ''",
            'email' => "varchar(255) NOT NULL DEFAULT ''",
            'phone' => "varchar(50) DEFAULT NULL",
            'qualification' => "varchar(255) DEFAULT NULL",
            'status' => "varchar(50) DEFAULT 'active'"
        ];

        foreach ($doctor_cols as $col => $def) {
            $exists = $wpdb->get_results("SHOW COLUMNS FROM `{$doctors}` LIKE '{$col}'");
            if (empty($exists)) {
                $wpdb->query("ALTER TABLE `{$doctors}` ADD COLUMN `{$col}` {$def}");
            }
        }

        $patient_cols = [
            'first_name' => "varchar(255) NOT NULL DEFAULT ''",
            'last_name' => "varchar(255) NOT NULL DEFAULT ''",
            'email' => "varchar(255) NOT NULL DEFAULT ''",
            'phone' => "varchar(50) DEFAULT NULL",
            'date_of_birth' => "date DEFAULT NULL",
            'address' => "text DEFAULT NULL",
            'status' => "varchar(50) DEFAULT 'active'"
        ];

        foreach ($patient_cols as $col => $def) {
            $exists = $wpdb->get_results("SHOW COLUMNS FROM `{$patients}` LIKE '{$col}'");
            if (empty($exists)) {
                $wpdb->query("ALTER TABLE `{$patients}` ADD COLUMN `{$col}` {$def}");
            }
        }
            
        update_option('clinic_temp_migrator_ran_v4', true);
        error_log('MANUAL COLUMN ADD SUCCESS');
    }
});
