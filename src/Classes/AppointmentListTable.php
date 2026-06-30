<?php

namespace ClinicManagement\Classes;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

use ClinicManagement\Repositories\AppointmentRepository;

class AppointmentListTable extends \WP_List_Table
{
    protected $appointmentRepo;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        parent::__construct([
            'singular' => 'appointment',
            'plural'   => 'appointments',
            'ajax'     => false
        ]);
        
        $this->appointmentRepo = $appointmentRepo;
    }

    public function get_columns()
    {
        return [
            'cb'               => '<input type="checkbox" />',
            'id'               => 'ID',
            'doctor_name'      => 'Doctor',
            'patient_name'     => 'Patient',
            'appointment_date' => 'Date',
            'start_time'       => 'Time',
            'status'           => 'Status'
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'id'               => ['id', false],
            'appointment_date' => ['appointment_date', false],
            'status'           => ['status', false],
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'doctor_name':
            case 'patient_name':
            case 'appointment_date':
            case 'start_time':
                return esc_html($item->$column_name);
            case 'status':
                $status_class = '';
                switch ($item->status) {
                    case 'pending': $status_class = 'notice-warning'; break;
                    case 'approved': $status_class = 'notice-success'; break;
                    case 'cancelled': $status_class = 'notice-error'; break;
                    case 'completed': $status_class = 'notice-info'; break;
                }
                return sprintf('<span class="notice %s" style="padding: 5px; display: inline-block;">%s</span>', $status_class, ucfirst(esc_html($item->status)));
            case 'id':
                return '#' . esc_html($item->id);
            default:
                return print_r($item, true);
        }
    }

    protected function column_id($item)
    {
        $id = '#' . esc_html($item->id);
        
        $edit_url = admin_url('admin.php?page=clinic-appointments&action=edit&id=' . $item->id);
        
        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=clinic-appointments&action=delete&id=' . $item->id),
            'delete_appointment_' . $item->id
        );

        $actions = [
            'edit'   => sprintf('<a href="%s">Manage / Reschedule</a>', $edit_url),
            'delete' => sprintf('<a href="%s" onclick="return confirm(\'Are you sure?\')">Delete</a>', $delete_url),
        ];

        return sprintf('%1$s %2$s', $id, $this->row_actions($actions));
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item->id
        );
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = [$columns, $hidden, $sortable];
        
        $data = $this->appointmentRepo->getWithRelations();
        
        usort($data, function ($a, $b) {
            $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'appointment_date';
            $order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'desc';
            
            $result = strcmp($a->$orderby ?? '', $b->$orderby ?? '');
            return ($order === 'asc') ? $result : -$result;
        });
        
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        
        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ]);
        
        $this->items = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
    }
}
