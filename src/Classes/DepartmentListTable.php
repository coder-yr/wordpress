<?php

namespace ClinicManagement\Classes;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

use ClinicManagement\Repositories\DepartmentRepository;

class DepartmentListTable extends \WP_List_Table
{
    protected $departmentRepo;

    public function __construct(DepartmentRepository $departmentRepo)
    {
        parent::__construct([
            'singular' => 'department',
            'plural'   => 'departments',
            'ajax'     => false
        ]);
        
        $this->departmentRepo = $departmentRepo;
    }

    public function get_columns()
    {
        return [
            'cb'          => '<input type="checkbox" />',
            'name'        => 'Name',
            'description' => 'Description',
            'status'      => 'Status'
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'name'   => ['name', false],
            'status' => ['status', false],
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'description':
            case 'status':
                return esc_html($item->$column_name);
            default:
                return print_r($item, true);
        }
    }

    protected function column_name($item)
    {
        $name = esc_html($item->name);
        
        $edit_url = admin_url('admin.php?page=clinic-departments&action=edit&id=' . $item->id);
        
        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=clinic-departments&action=delete&id=' . $item->id),
            'delete_department_' . $item->id
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
        
        $data = $this->departmentRepo->all();
        
        usort($data, function ($a, $b) {
            $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'name';
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
