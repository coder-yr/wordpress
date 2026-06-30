<?php

namespace ClinicManagement\Classes;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

use ClinicManagement\Repositories\PatientRepository;

class PatientListTable extends \WP_List_Table
{
    protected $patientRepo;

    public function __construct(PatientRepository $patientRepo)
    {
        parent::__construct([
            'singular' => 'patient',
            'plural'   => 'patients',
            'ajax'     => false
        ]);
        
        $this->patientRepo = $patientRepo;
    }

    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox" />',
            'first_name'    => 'Name',
            'email'         => 'Email',
            'phone'         => 'Phone',
            'gender'        => 'Gender',
            'date_of_birth' => 'DOB',
            'blood_group'   => 'Blood Group',
            'status'        => 'Status'
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'first_name' => ['first_name', false],
            'email'      => ['email', false],
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'email':
            case 'phone':
            case 'gender':
            case 'date_of_birth':
            case 'blood_group':
            case 'status':
                return esc_html($item->$column_name);
            default:
                return print_r($item, true);
        }
    }

    protected function column_first_name($item)
    {
        $name = esc_html($item->first_name . ' ' . $item->last_name);
        
        $edit_url = admin_url('admin.php?page=clinic-patients&action=edit&id=' . $item->id);
        
        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=clinic-patients&action=delete&id=' . $item->id),
            'delete_patient_' . $item->id
        );

        $actions = [
            'edit'   => sprintf('<a href="%s">Edit</a>', $edit_url),
            'delete' => sprintf('<a href="%s" onclick="return confirm(\'Are you sure?\')">Delete</a>', $delete_url),
        ];

        return sprintf('%1$s %2$s', $name, $this->row_actions($actions));
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
        
        $data = $this->patientRepo->all();
        
        usort($data, function ($a, $b) {
            $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'first_name';
            $order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'asc';
            
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
