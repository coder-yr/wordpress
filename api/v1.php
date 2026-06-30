<?php

/** @var \ClinicManagement\Bootstrap\Router $router */

$router->get('/health', function () {
    return rest_ensure_response([
        'status' => 'ok',
        'message' => 'Clinic API is running'
    ]);
});

$router->get('/doctors', 'ClinicManagement\Controllers\DoctorController@index');
$router->get('/doctors/(?P<id>\d+)/availability', 'ClinicManagement\Controllers\DoctorController@availability');

// Protected Routes (Patients & Receptionists)
$router->post('/appointments', [
    'ClinicManagement\Controllers\AppointmentController', 
    'store'
], [
    'permission_callback' => [\ClinicManagement\Middlewares\AuthMiddleware::class, 'canBookAppointment']
]);

// Protected Routes (Doctors)
$router->get('/appointments/(?P<id>\d+)', [
    'ClinicManagement\Controllers\AppointmentController', 
    'show'
], [
    'permission_callback' => [\ClinicManagement\Middlewares\AuthMiddleware::class, 'canViewAppointment']
]);
