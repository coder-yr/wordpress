<?php

namespace ClinicManagement\Bootstrap;

class AdminServiceProvider extends ServiceProvider
{
    public function register()
    {
        error_log(__CLASS__ . ' register');
        // Bind backend controllers if necessary, but we can rely on automatic resolution
    }

    public function boot()
    {
        error_log(__CLASS__ . ' boot');

        add_action('admin_menu', [$this, 'registerAdminMenus']);

        // Enqueue admin assets
        add_action('admin_enqueue_scripts', function($hook) {
            // Only load on our plugin pages
            if (strpos($hook, 'clinic-') !== false) {
                wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');
                wp_enqueue_style('clinic-modern-admin', plugin_dir_url(CLINIC_PLUGIN_FILE) . 'assets/css/clinic-modern.css', ['bootstrap-css'], '1.0.0');
            }
        });
    }

    public function registerAdminMenus()
    {
        // Require 'manage_options' capability to access the Admin Module
        $capability = 'manage_options';

        // Main Menu - Dashboard
        add_menu_page(
            'Clinic Management Dashboard', // Page title
            'Clinic',                      // Menu title
            $capability,                   // Capability
            'clinic-management',           // Menu slug
            [$this, 'renderDashboard'],    // Callback function
            'dashicons-clipboard',         // Icon url
            30                             // Position
        );

        // Submenus
        add_submenu_page(
            'clinic-management',
            'Dashboard',
            'Dashboard',
            $capability,
            'clinic-management',
            [$this, 'renderDashboard']
        );

        add_submenu_page(
            'clinic-management',
            'Doctors',
            'Doctors',
            $capability,
            'clinic-doctors',
            [$this, 'renderDoctors']
        );

        add_submenu_page(
            'clinic-management',
            'Patients',
            'Patients',
            $capability,
            'clinic-patients',
            [$this, 'renderPatients']
        );

        add_submenu_page(
            'clinic-management',
            'Departments',
            'Departments',
            $capability,
            'clinic-departments',
            [$this, 'renderDepartments']
        );

        add_submenu_page(
            'clinic-management',
            'Appointments',
            'Appointments',
            $capability,
            'clinic-appointments',
            [$this, 'renderAppointments']
        );

        add_submenu_page(
            'clinic-management',
            'Availability',
            'Availability',
            $capability,
            'clinic-availability',
            [$this, 'renderAvailability']
        );

        add_submenu_page(
            'clinic-management',
            'Reports',
            'Reports',
            $capability,
            'clinic-reports',
            [$this, 'renderPlaceholder']
        );

        add_submenu_page(
            'clinic-management',
            'Settings',
            'Settings',
            $capability,
            'clinic-settings',
            [$this, 'renderPlaceholder']
        );
    }

    public function renderDashboard()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\DashboardController::class);
        $controller->index();
    }

    public function renderDoctors()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\DoctorController::class);
        
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'index';

        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } else {
            $controller->index();
        }
    }

    public function renderDepartments()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\DepartmentController::class);
        
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'index';

        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } else {
            $controller->index();
        }
    }

    public function renderPatients()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\PatientController::class);
        
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'index';

        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } else {
            $controller->index();
        }
    }

    public function renderAppointments()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\AppointmentController::class);
        
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'index';

        if ($action === 'edit') {
            $controller->edit();
        } else {
            $controller->index();
        }
    }

    public function renderAvailability()
    {
        $controller = $this->app->make(\ClinicManagement\Controllers\Admin\AvailabilityController::class);
        $controller->index();
    }

    public function renderPlaceholder()
    {
        echo '<div class="wrap"><h1>Coming Soon</h1><p>This module will be built in the next milestone.</p></div>';
    }
}
