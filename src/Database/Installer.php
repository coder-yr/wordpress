<?php

namespace ClinicManagement\Database;

use ClinicManagement\Bootstrap\Application;

class Installer
{
    /**
     * Run the installation process on plugin activation.
     */
    public static function run()
    {
        $app = Application::getInstance();

        // 1. Run Database Migrations
        $migrator = new Migrator($app->make('config'));
        $migrator->up();

        // 2. Run Seeders
        $roleSeeder = new \ClinicManagement\Database\Seeders\RoleSeeder();
        $roleSeeder->run();

        // 3. Create Default Pages
        self::createDefaultPages();

        // 4. Flush Rewrite Rules
        flush_rewrite_rules();
    }

    /**
     * Create required WordPress pages for dashboards.
     */
    private static function createDefaultPages()
    {
        $pages = [
            'patient-dashboard' => [
                'title'   => 'Patient Dashboard',
                'content' => '[clinic_patient_dashboard]'
            ],
            'doctor-dashboard' => [
                'title'   => 'Doctor Dashboard',
                'content' => '[clinic_doctor_dashboard]'
            ]
        ];

        foreach ($pages as $slug => $page) {
            $existing = get_page_by_path($slug);
            if (!$existing) {
                wp_insert_post([
                    'post_name'    => $slug,
                    'post_title'   => $page['title'],
                    'post_content' => $page['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page'
                ]);
            }
        }
    }
}
