<?php

namespace ClinicManagement\Database\Seeders;

class RoleSeeder
{
    /**
     * Run the database seeds for WordPress Roles and Capabilities.
     */
    public function run()
    {
        // Administrator is standard WP role, but we add custom caps to them if needed
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_clinic_options');
            $admin->add_cap('manage_clinic_doctors');
            $admin->add_cap('manage_clinic_patients');
        }

        // Add Clinic Doctor Role
        add_role('clinic_doctor', 'Clinic Doctor', [
            'read' => true,
            'edit_clinic_appointments' => true,
            'view_clinic_reports'      => true,
            'write_clinic_prescriptions'=> true,
        ]);

        // Add Clinic Patient Role
        add_role('clinic_patient', 'Clinic Patient', [
            'read' => true,
            'book_clinic_appointments' => true,
            'view_own_reports'         => true,
        ]);

        // Add Clinic Receptionist Role
        add_role('clinic_receptionist', 'Clinic Receptionist', [
            'read' => true,
            'book_clinic_appointments' => true,
            'edit_clinic_appointments' => true,
            'view_clinic_doctors'      => true,
            'view_clinic_patients'     => true,
        ]);
    }
}
