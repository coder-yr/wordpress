<?php

namespace ClinicManagement\Repositories;

class AppointmentRepository extends BaseRepository
{
    protected function setTable()
    {
        $this->table = $this->config->get('database.tables.appointments');
    }

    /**
     * Get appointments for a specific doctor on a specific date.
     *
     * @param int $doctorId
     * @param string $date (Y-m-d)
     * @return array
     */
    public function getDoctorAppointmentsOnDate(int $doctorId, string $date): array
    {
        $sql = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE doctor_id = %d AND appointment_date = %s",
            $doctorId,
            $date
        );
        
        return $this->db->get_results($sql) ?: [];
    }

    /**
     * Check if a specific time slot is already booked for a doctor.
     *
     * @param int $doctorId
     * @param string $date
     * @param string $startTime
     * @return bool
     */
    public function isSlotBooked(int $doctorId, string $date, string $startTime): bool
    {
        $sql = $this->db->prepare(
            "SELECT id FROM {$this->table} WHERE doctor_id = %d AND appointment_date = %s AND start_time = %s AND status != 'cancelled' LIMIT 1",
            $doctorId,
            $date,
            $startTime
        );

        return $this->db->get_var($sql) !== null;
    }

    /**
     * Get count of today's appointments.
     *
     * @return int
     */
    public function countTodays(): int
    {
        $today = current_time('Y-m-d');
        $sql = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE appointment_date = %s", $today);
        return (int) $this->db->get_var($sql);
    }

    /**
     * Get count of upcoming appointments (future dates).
     *
     * @return int
     */
    public function countUpcoming(): int
    {
        $today = current_time('Y-m-d');
        $sql = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE appointment_date > %s", $today);
        return (int) $this->db->get_var($sql);
    }

    /**
     * Get all appointments with doctor and patient names.
     *
     * @return array
     */
    public function getWithRelations(): array
    {
        $doctorTable = $this->config->get('database.tables.doctors');
        $patientTable = $this->config->get('database.tables.patients');

        $sql = "SELECT a.*, 
                CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM {$this->table} a
                LEFT JOIN {$doctorTable} d ON a.doctor_id = d.id
                LEFT JOIN {$patientTable} p ON a.patient_id = p.id
                ORDER BY a.appointment_date DESC, a.start_time DESC";

        return $this->db->get_results($sql) ?: [];
    }

    /**
     * Get all appointments for a specific patient.
     *
     * @param int $patientId
     * @return array
     */
    public function getPatientAppointments(int $patientId): array
    {
        $doctorTable = $this->config->get('database.tables.doctors');
        $departmentTable = $this->config->get('database.tables.departments');

        $sql = $this->db->prepare("
            SELECT a.*, 
            CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
            dep.name as department_name
            FROM {$this->table} a
            LEFT JOIN {$doctorTable} d ON a.doctor_id = d.id
            LEFT JOIN {$departmentTable} dep ON a.department_id = dep.id
            WHERE a.patient_id = %d
            ORDER BY a.appointment_date DESC, a.start_time DESC
        ", $patientId);

        return $this->db->get_results($sql) ?: [];
    }
}
