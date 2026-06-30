<?php

namespace ClinicManagement\Events;

use ClinicManagement\Bootstrap\Application;

class Dispatcher
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a listener for an event.
     *
     * @param string $eventClass
     * @param string $listenerClass
     */
    public function listen(string $eventClass, string $listenerClass)
    {
        $this->listeners[$eventClass][] = $listenerClass;
    }

    /**
     * Dispatch an event to all its registered listeners.
     *
     * @param object $event
     */
    public function dispatch($event)
    {
        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return;
        }

        foreach ($this->listeners[$eventClass] as $listenerClass) {
            $listener = $this->app->make($listenerClass);
            $listener->handle($event);
        }
    }
}
