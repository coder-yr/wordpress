<?php

namespace ClinicManagement\Bootstrap;

class Router
{
    /**
     * @var Application
     */
    protected $app;

    protected $routes = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a GET route
     */
    public function get($route, $handler, $args = [])
    {
        $this->addRoute(\WP_REST_Server::READABLE, $route, $handler, $args);
    }

    /**
     * Register a POST route
     */
    public function post($route, $handler, $args = [])
    {
        $this->addRoute(\WP_REST_Server::CREATABLE, $route, $handler, $args);
    }

    /**
     * Register a PUT route
     */
    public function put($route, $handler, $args = [])
    {
        $this->addRoute(\WP_REST_Server::EDITABLE, $route, $handler, $args);
    }

    /**
     * Register a DELETE route
     */
    public function delete($route, $handler, $args = [])
    {
        $this->addRoute(\WP_REST_Server::DELETABLE, $route, $handler, $args);
    }

    protected function addRoute($methods, $route, $handler, $args = [])
    {
        $this->routes[] = [
            'methods' => $methods,
            'route'   => $route,
            'handler' => $handler,
            'args'    => $args
        ];
    }

    public function registerRoutes()
    {
        $namespace = $this->app->make('config')->get('app.api_namespace', 'clinic/v1');

        foreach ($this->routes as $r) {
            register_rest_route($namespace, $r['route'], array_merge([
                'methods'  => $r['methods'],
                'callback' => $this->resolveCallback($r['handler']),
                'permission_callback' => '__return_true' // TODO: integrate Policies
            ], $r['args']));
        }
    }

    protected function resolveCallback($handler)
    {
        if (is_callable($handler)) {
            return $handler;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($class, $method) = explode('@', $handler);
            return function (\WP_REST_Request $request) use ($class, $method) {
                $controller = $this->app->make($class);
                return $controller->{$method}($request);
            };
        }

        if (is_array($handler) && count($handler) == 2) {
            return function (\WP_REST_Request $request) use ($handler) {
                $controller = $this->app->make($handler[0]);
                return $controller->{$handler[1]}($request);
            };
        }

        throw new \Exception("Invalid route handler format.");
    }
}
