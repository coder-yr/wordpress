<?php
// tests/bootstrap.php

// This is a minimal bootstrap for PHPUnit
require_once dirname(__DIR__) . '/bootstrap/Loader.php';

// Register the custom autoloader for tests
$loader = new \ClinicManagement\Bootstrap\Loader();
$loader->register();
$loader->addNamespace('ClinicManagement\\', dirname(__DIR__));
$loader->addNamespace('Tests\\', __DIR__);
