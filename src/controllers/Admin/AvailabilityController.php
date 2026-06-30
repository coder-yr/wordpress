<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\AvailabilityRepository;
use ClinicManagement\Repositories\LeaveRepository;
use ClinicManagement\Repositories\DoctorRepository;

class AvailabilityController
{
    protected $availabilityRepo;
    protected $leaveRepo;
    protected $doctorRepo;

    public function __construct(
        AvailabilityRepository $availabilityRepo,
        LeaveRepository $leaveRepo,
        DoctorRepository $doctorRepo
    ) {
        $this->availabilityRepo = $availabilityRepo;
        $this->leaveRepo = $leaveRepo;
        $this->doctorRepo = $doctorRepo;
    }

    public function index()
    {
        $doctors = $this->doctorRepo->all();
        $selectedDoctorId = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
        
        $availability = [];
        $leaves = [];
        
        if ($selectedDoctorId > 0) {
            // Handle updates
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_availability'])) {
                if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'update_availability_nonce')) {
                    $schedule = $_POST['schedule'] ?? [];
                    foreach ($schedule as $day => $times) {
                        $start = sanitize_text_field($times['start'] ?? '');
                        $end = sanitize_text_field($times['end'] ?? '');
                        
                        if (!empty($start) && !empty($end)) {
                            $this->availabilityRepo->updateAvailability($selectedDoctorId, (int) $day, $start, $end);
                        }
                    }
                    echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-availability&doctor_id=' . $selectedDoctorId . '&updated=1')) . "');</script>";
                    exit;
                }
            }
            
            // Handle new leave
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_leave'])) {
                if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'add_leave_nonce')) {
                    $date = sanitize_text_field($_POST['leave_date'] ?? '');
                    $reason = sanitize_text_field($_POST['reason'] ?? '');
                    
                    if (!empty($date)) {
                        $this->leaveRepo->create([
                            'doctor_id' => $selectedDoctorId,
                            'date' => $date,
                            'reason' => $reason
                        ]);
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-availability&doctor_id=' . $selectedDoctorId . '&leave_added=1')) . "');</script>";
                        exit;
                    }
                }
            }
            
            // Handle delete leave
            if (isset($_GET['action']) && $_GET['action'] === 'delete_leave' && isset($_GET['leave_id'])) {
                $leaveId = (int) $_GET['leave_id'];
                if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_leave_' . $leaveId)) {
                    $this->leaveRepo->delete($leaveId);
                    echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-availability&doctor_id=' . $selectedDoctorId . '&leave_deleted=1')) . "');</script>";
                    exit;
                }
            }

            // Fetch data for view
            $rawAvailability = $this->availabilityRepo->getByDoctorId($selectedDoctorId);
            foreach ($rawAvailability as $av) {
                $availability[$av->day_of_week] = $av;
            }
            
            $leaves = $this->leaveRepo->getByDoctorId($selectedDoctorId);
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/availability/index.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }
}
