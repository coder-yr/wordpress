<div class="wrap">
    <h1 class="wp-heading-inline">Add New Department</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-departments')); ?>" class="page-title-action">Back to List</a>
    <hr class="wp-header-end">

    <?php if (!empty($errors)) : ?>
        <div class="notice notice-error">
            <ul>
                <?php foreach ((array)$errors as $error) : ?>
                    <li><?php echo esc_html($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field('create_department_nonce'); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="name">Department Name <span class="description">(required)</span></label></th>
                    <td><input name="department[name]" type="text" id="name" value="<?php echo esc_attr($data['name'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description">Description</label></th>
                    <td><textarea name="department[description]" id="description" class="large-text" rows="5"><?php echo esc_textarea($data['description'] ?? ''); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">Status</label></th>
                    <td>
                        <select name="department[status]" id="status">
                            <option value="active" <?php selected($data['status'] ?? 'active', 'active'); ?>>Active</option>
                            <option value="inactive" <?php selected($data['status'] ?? '', 'inactive'); ?>>Inactive</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('Add Department', 'primary', 'submit_department'); ?>
    </form>
</div>
