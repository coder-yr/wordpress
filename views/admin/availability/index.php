<div class="wrap">
    <h1 class="wp-heading-inline">Availability & Leaves</h1>
    <hr class="wp-header-end">

    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Schedule updated successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['leave_added']) && $_GET['leave_added'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Leave added successfully.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['leave_deleted']) && $_GET['leave_deleted'] == '1') : ?>
        <div class="notice notice-success is-dismissible"><p>Leave removed successfully.</p></div>
    <?php endif; ?>

    <!-- Doctor Selector -->
    <div class="tablenav top">
        <form method="get">
            <input type="hidden" name="page" value="clinic-availability">
            <div class="alignleft actions">
                <select name="doctor_id" id="doctor_id">
                    <option value="0">-- Select a Doctor --</option>
                    <?php foreach ($doctors as $doctor) : ?>
                        <option value="<?php echo esc_attr($doctor->id); ?>" <?php selected($selectedDoctorId, $doctor->id); ?>>
                            <?php echo esc_html($doctor->first_name . ' ' . $doctor->last_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php submit_button('View Schedule', 'button', '', false); ?>
            </div>
        </form>
    </div>

    <?php if ($selectedDoctorId > 0) : ?>
        <div style="display: flex; gap: 30px; margin-top: 20px;">
            <!-- Weekly Schedule Form -->
            <div style="flex: 2;">
                <h2>Weekly Schedule</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('update_availability_nonce'); ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $days = [
                                1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 
                                4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
                            ];
                            foreach ($days as $num => $name) :
                                $start = isset($availability[$num]) ? substr($availability[$num]->start_time, 0, 5) : '';
                                $end = isset($availability[$num]) ? substr($availability[$num]->end_time, 0, 5) : '';
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($name); ?></strong></td>
                                <td>
                                    <input type="time" name="schedule[<?php echo $num; ?>][start]" value="<?php echo esc_attr($start); ?>" class="regular-text">
                                </td>
                                <td>
                                    <input type="time" name="schedule[<?php echo $num; ?>][end]" value="<?php echo esc_attr($end); ?>" class="regular-text">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p>
                        <?php submit_button('Save Weekly Schedule', 'primary', 'submit_availability', false); ?>
                    </p>
                </form>
            </div>

            <!-- Leaves List & Add -->
            <div style="flex: 1;">
                <h2>Manage Leaves</h2>
                <div class="postbox" style="padding: 15px;">
                    <h3>Add Leave Date</h3>
                    <form method="post" action="">
                        <?php wp_nonce_field('add_leave_nonce'); ?>
                        <p>
                            <label for="leave_date">Date</label><br>
                            <input type="date" name="leave_date" id="leave_date" required class="widefat">
                        </p>
                        <p>
                            <label for="reason">Reason (optional)</label><br>
                            <input type="text" name="reason" id="reason" class="widefat">
                        </p>
                        <p>
                            <?php submit_button('Add Leave', 'secondary', 'submit_leave', false); ?>
                        </p>
                    </form>
                </div>

                <h3>Upcoming Leaves</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaves)) : ?>
                            <tr><td colspan="3">No leaves scheduled.</td></tr>
                        <?php else : ?>
                            <?php foreach ($leaves as $leave) : ?>
                                <tr>
                                    <td><?php echo esc_html($leave->date); ?></td>
                                    <td><?php echo esc_html($leave->reason); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=clinic-availability&doctor_id=' . $selectedDoctorId . '&action=delete_leave&leave_id=' . $leave->id), 'delete_leave_' . $leave->id)); ?>" onclick="return confirm('Are you sure?')" style="color: #a00;">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <p>Please select a doctor from the dropdown above to view and manage their schedule.</p>
    <?php endif; ?>
</div>
