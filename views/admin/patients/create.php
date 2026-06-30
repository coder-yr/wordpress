<div class="wrap">
    <h1 class="wp-heading-inline">Add New Patient</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-patients')); ?>" class="page-title-action">Back to List</a>
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
        <?php wp_nonce_field('create_patient_nonce'); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="first_name">First Name <span class="description">(required)</span></label></th>
                    <td><input name="patient[first_name]" type="text" id="first_name" value="<?php echo esc_attr($data['first_name'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="last_name">Last Name <span class="description">(required)</span></label></th>
                    <td><input name="patient[last_name]" type="text" id="last_name" value="<?php echo esc_attr($data['last_name'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email">Email <span class="description">(required)</span></label></th>
                    <td><input name="patient[email]" type="email" id="email" value="<?php echo esc_attr($data['email'] ?? ''); ?>" class="regular-text ltr" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="phone">Phone <span class="description">(required)</span></label></th>
                    <td><input name="patient[phone]" type="text" id="phone" value="<?php echo esc_attr($data['phone'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gender">Gender</label></th>
                    <td>
                        <select name="patient[gender]" id="gender">
                            <option value="male" <?php selected($data['gender'] ?? '', 'male'); ?>>Male</option>
                            <option value="female" <?php selected($data['gender'] ?? '', 'female'); ?>>Female</option>
                            <option value="other" <?php selected($data['gender'] ?? 'other', 'other'); ?>>Other</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="date_of_birth">Date of Birth <span class="description">(required)</span></label></th>
                    <td><input name="patient[date_of_birth]" type="date" id="date_of_birth" value="<?php echo esc_attr($data['date_of_birth'] ?? ''); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="blood_group">Blood Group</label></th>
                    <td>
                        <select name="patient[blood_group]" id="blood_group">
                            <option value="">Unknown</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg) : ?>
                                <option value="<?php echo esc_attr($bg); ?>" <?php selected($data['blood_group'] ?? '', $bg); ?>><?php echo esc_html($bg); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="address">Address</label></th>
                    <td><textarea name="patient[address]" id="address" class="large-text" rows="3"><?php echo esc_textarea($data['address'] ?? ''); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="emergency_contact">Emergency Contact</label></th>
                    <td><input name="patient[emergency_contact]" type="text" id="emergency_contact" value="<?php echo esc_attr($data['emergency_contact'] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="medical_history">Medical History</label></th>
                    <td><textarea name="patient[medical_history]" id="medical_history" class="large-text" rows="5"><?php echo esc_textarea($data['medical_history'] ?? ''); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">Status</label></th>
                    <td>
                        <select name="patient[status]" id="status">
                            <option value="active" <?php selected($data['status'] ?? 'active', 'active'); ?>>Active</option>
                            <option value="inactive" <?php selected($data['status'] ?? '', 'inactive'); ?>>Inactive</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('Add Patient', 'primary', 'submit_patient'); ?>
    </form>
</div>
