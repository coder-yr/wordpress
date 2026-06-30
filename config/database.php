<?php

global $wpdb;
$prefix = $wpdb->prefix . 'clinic_';

return [
    'tables' => [
        'doctors'       => $prefix . 'doctors',
        'patients'      => $prefix . 'patients',
        'departments'   => $prefix . 'departments',
        'appointments'  => $prefix . 'appointments',
        'availability'  => $prefix . 'availability',
        'leaves'        => $prefix . 'leaves',
        'consultations' => $prefix . 'consultations',
        'prescriptions' => $prefix . 'prescriptions',
        'reports'       => $prefix . 'medical_reports',
    ],
    'migrations_table' => $prefix . 'migrations'
];
