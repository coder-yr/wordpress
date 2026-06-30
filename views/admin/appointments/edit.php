<div class="wrap">
    <h1 class="wp-heading-inline">Manage Appointment #<?php echo esc_html($appointment->id); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=clinic-appointments')); ?>" class="page-title-action">Back to List</a>
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

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <h2>Appointment Details</h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Patient</th>
                        <td><strong><?php echo esc_html($patient->first_name . ' ' . $patient->last_name); ?></strong> (<?php echo esc_html($patient->email); ?>)</td>
                    </tr>
                    <tr>
                        <th scope="row">Doctor</th>
                        <td><strong><?php echo esc_html($doctor->first_name . ' ' . $doctor->last_name); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row">Created At</th>
                        <td><?php echo esc_html($appointment->created_at); ?></td>
                    </tr>
                </tbody>
            </table>

            <hr>

            <h2>Update Status</h2>
            <form method="post" action="">
                <?php wp_nonce_field('edit_appointment_nonce'); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="status">Status</label></th>
                            <td>
                                <select name="status" id="status">
                                    <option value="pending" <?php selected($data['status'], 'pending'); ?>>Pending</option>
                                    <option value="approved" <?php selected($data['status'], 'approved'); ?>>Approved</option>
                                    <option value="completed" <?php selected($data['status'], 'completed'); ?>>Completed</option>
                                    <option value="cancelled" <?php selected($data['status'], 'cancelled'); ?>>Cancelled</option>
                                    <option value="no_show" <?php selected($data['status'], 'no_show'); ?>>No Show</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button('Update Status', 'primary', 'submit_appointment'); ?>
            </form>
        </div>

        <div style="flex: 1; border-left: 1px solid #ccc; padding-left: 20px;">
            <h2>Reschedule Appointment</h2>
            <form method="post" action="">
                <?php wp_nonce_field('edit_appointment_nonce'); ?>
                <input type="hidden" name="reschedule" value="1">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="appointment_date">New Date</label></th>
                            <td><input name="appointment[appointment_date]" type="date" id="appointment_date" value="<?php echo esc_attr($data['appointment_date']); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="appointment_time">New Time</label></th>
                            <td><input name="appointment[appointment_time]" type="time" id="appointment_time" value="<?php echo esc_attr($data['appointment_time'] ?? $data['start_time']); ?>" class="regular-text" required></td>
                        </tr>
                    </tbody>
                </table>
                <p class="description">Rescheduling will automatically change the status to Approved.</p>
                <?php submit_button('Reschedule', 'secondary', 'submit_appointment'); ?>
            </form>
        </div>
    </div>
</div>
