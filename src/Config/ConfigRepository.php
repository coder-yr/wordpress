<?php

namespace ClinicManagement\Config;

class ConfigRepository
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct()
    {
        $this->loadConfigurationFiles();
    }

    /**
     * Load all configuration files from the config directory.
     */
    protected function loadConfigurationFiles()
    {
        $configPath = CLINIC_PLUGIN_DIR . 'config/';
        $files = [
            'app.php',
            'database.php',
            'notifications.php',
            'roles.php'
        ];

        foreach ($files as $file) {
            $path = $configPath . $file;
            if (file_exists($path)) {
                $key = basename($file, '.php');
                $this->items[$key] = require $path;
            }
        }
    }

    /**
     * Get the specified configuration value using "dot" notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->items;
        
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
