<?php

namespace ClinicManagement\Bootstrap;

class RouteServiceProvider extends ServiceProvider
{
    public function register()
    {
        error_log(__CLASS__ . ' register');

        $this->app->singleton('router', function ($app) {
            return new Router($app);
        });
    }

    public function boot()
    {
        error_log(__CLASS__ . ' boot');

        add_action('rest_api_init', function () {
            // Load routes from the api directory
            $apiPath = CLINIC_PLUGIN_DIR . 'api/v1.php';
            if (file_exists($apiPath)) {
                $router = $this->app->make('router');
                require $apiPath;
                $router->registerRoutes();
            }
        });
    }
}
