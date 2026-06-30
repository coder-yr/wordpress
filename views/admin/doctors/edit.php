<div class="wrap">
    <h1 class="wp-heading-inline">Edit Doctor</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-doctors')); ?>" class="page-title-action">Back to List</a>
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
        <?php wp_nonce_field('edit_doctor_nonce'); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="first_name">First Name <span class="description">(required)</span></label></th>
                    <td><input name="doctor[first_name]" type="text" id="first_name" value="<?php echo esc_attr($data['first_name'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="last_name">Last Name <span class="description">(required)</span></label></th>
                    <td><input name="doctor[last_name]" type="text" id="last_name" value="<?php echo esc_attr($data['last_name'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email">Email <span class="description">(required)</span></label></th>
                    <td><input name="doctor[email]" type="email" id="email" value="<?php echo esc_attr($data['email'] ?? ''); ?>" class="regular-text ltr" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="phone">Phone <span class="description">(required)</span></label></th>
                    <td><input name="doctor[phone]" type="text" id="phone" value="<?php echo esc_attr($data['phone'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="department_id">Department ID <span class="description">(required)</span></label></th>
                    <td><input name="doctor[department_id]" type="number" id="department_id" value="<?php echo esc_attr($data['department_id'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="specialization">Specialization</label></th>
                    <td><input name="doctor[specialization]" type="text" id="specialization" value="<?php echo esc_attr($data['specialization'] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="qualification">Qualification</label></th>
                    <td><input name="doctor[qualification]" type="text" id="qualification" value="<?php echo esc_attr($data['qualification'] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="experience_years">Experience (Years)</label></th>
                    <td><input name="doctor[experience_years]" type="number" id="experience_years" value="<?php echo esc_attr($data['experience_years'] ?? '0'); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="consultation_fee">Consultation Fee <span class="description">(required)</span></label></th>
                    <td><input name="doctor[consultation_fee]" type="number" step="0.01" id="consultation_fee" value="<?php echo esc_attr($data['consultation_fee'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">Status</label></th>
                    <td>
                        <select name="doctor[status]" id="status">
                            <option value="active" <?php selected($data['status'] ?? 'active', 'active'); ?>>Active</option>
                            <option value="inactive" <?php selected($data['status'] ?? '', 'inactive'); ?>>Inactive</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('Update Doctor', 'primary', 'submit_doctor'); ?>
    </form>
</div>
