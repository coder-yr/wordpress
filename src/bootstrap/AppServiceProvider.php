<?php

namespace ClinicManagement\Bootstrap;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        error_log(__CLASS__ . ' register');

        // Bind repositories and services if they need interface mapping
        
        $this->app->singleton(\ClinicManagement\Events\Dispatcher::class, function ($app) {
            $dispatcher = new \ClinicManagement\Events\Dispatcher($app);
            
            // Register listeners
            $dispatcher->listen(
                \ClinicManagement\Events\AppointmentBooked::class, 
                \ClinicManagement\Listeners\SendBookingEmail::class
            );
            $dispatcher->listen(
                \ClinicManagement\Events\AppointmentBooked::class, 
                \ClinicManagement\Listeners\LogActivity::class
            );
            
            return $dispatcher;
        });
    }

    public function boot()
    {
        error_log(__CLASS__ . ' boot');

        // Setup installer hook
        register_activation_hook(CLINIC_PLUGIN_FILE, [\ClinicManagement\Database\Installer::class, 'run']);

        // Register custom cron jobs
        add_action('init', [\ClinicManagement\Jobs\AppointmentReminder::class, 'schedule']);
        add_action('clinic_appointment_reminders', [\ClinicManagement\Jobs\AppointmentReminder::class, 'handle']);

        // Register shortcodes
        add_action('init', function () {
            $frontend = $this->app->make(\ClinicManagement\Controllers\FrontendController::class);
            $frontend->registerShortcodes();
        });

        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_style('clinic-modern', plugin_dir_url(CLINIC_PLUGIN_FILE) . 'assets/css/clinic-modern.css', [], '1.0.0');
        });
    }
}
