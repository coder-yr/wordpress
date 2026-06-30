<?php

namespace ClinicManagement\Bootstrap;

class Application
{
    /**
     * @var Application|null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var array
     */
    private $instances = [];

    /**
     * Private constructor to enforce Singleton pattern.
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton instance of the Application.
     *
     * @return Application
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
            // Bind itself into the container to allow DI resolution
            self::$instance->instances[self::class] = self::$instance;
        }
        return self::$instance;
    }

    /**
     * Bind a class or interface to a concrete implementation.
     *
     * @param string $abstract
     * @param string|\Closure $concrete
     */
    public function bind(string $abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Bind an abstract to a singleton instance.
     *
     * @param string $abstract
     * @param string|\Closure $concrete
     */
    public function singleton(string $abstract, $concrete)
    {
        $this->bind($abstract, function ($app) use ($abstract, $concrete) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $this->build($concrete);
            }
            return $this->instances[$abstract];
        });
    }

    /**
     * Resolve a dependency.
     *
     * @param string $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;
        return $this->build($concrete);
    }

    /**
     * Build the concrete instance.
     *
     * @param mixed $concrete
     * @return mixed
     */
    private function build($concrete)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        try {
            $reflector = new \ReflectionClass($concrete);
        } catch (\ReflectionException $e) {
            throw new \Exception("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Target class [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve constructor dependencies.
     *
     * @param \ReflectionParameter[] $dependencies
     * @return array
     */
    private function resolveDependencies(array $dependencies): array
    {
        $results = [];
        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();
            if ($type && !$type->isBuiltin()) {
                $results[] = $this->make($type->getName());
            } else {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve dependency {$dependency->name}");
                }
            }
        }
        return $results;
    }

    /**
     * Boot the application.
     */
    public function boot()
    {
        error_log('Clinic Management Booted');

        $app = Application::getInstance();
        $config_instance = $app->make(\ClinicManagement\Config\ConfigRepository::class);
        error_log(get_class($config_instance));
        // Load configuration
        $this->singleton('config', function () {
            return new \ClinicManagement\Config\ConfigRepository();
        });

        // Register Service Providers
        $providers = [
            \ClinicManagement\Bootstrap\AppServiceProvider::class,
            \ClinicManagement\Bootstrap\RouteServiceProvider::class,
            \ClinicManagement\Bootstrap\AdminServiceProvider::class,
        ];

        foreach ($providers as $providerClass) {
            /** @var ServiceProvider $provider */
            $provider = $this->make($providerClass);
            $provider->register();
        }

        foreach ($providers as $providerClass) {
            /** @var ServiceProvider $provider */
            $provider = $this->make($providerClass);
            $provider->boot();
        }
    }
}
