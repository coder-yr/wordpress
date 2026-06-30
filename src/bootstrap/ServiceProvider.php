<?php

namespace ClinicManagement\Bootstrap;

abstract class ServiceProvider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register bindings in the container.
     */
    abstract public function register();

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Default empty boot method
    }
}
