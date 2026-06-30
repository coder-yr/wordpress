<?php

namespace ClinicManagement\Middlewares;

use ClinicManagement\Policies\AppointmentPolicy;

class AuthMiddleware
{
    /**
     * Check if user can book an appointment.
     *
     * @return bool|\WP_Error
     */
    public static function canBookAppointment()
    {
        if (!is_user_logged_in()) {
            return new \WP_Error('unauthorized', 'You must be logged in to book an appointment.', ['status' => 401]);
        }

        $user = wp_get_current_user();
        if (!AppointmentPolicy::canBook($user)) {
            return new \WP_Error('forbidden', 'You do not have permission to book appointments.', ['status' => 403]);
        }

        return true;
    }

    /**
     * Check if user can view an appointment.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public static function canViewAppointment(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return new \WP_Error('unauthorized', 'You must be logged in.', ['status' => 401]);
        }

        $user = wp_get_current_user();
        $appointmentId = (int) $request->get_param('id');

        if (!AppointmentPolicy::canView($user, $appointmentId)) {
            return new \WP_Error('forbidden', 'You do not have permission to view this appointment.', ['status' => 403]);
        }

        return true;
    }
}
