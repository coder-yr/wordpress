<div class="wrap clinic-dashboard clinic-modern-wrapper">
    <div class="container-fluid">
        <h1 class="wp-heading-inline mb-4" style="color: var(--clinic-dark); font-weight: 700; font-family: 'Inter', sans-serif; display: none;">Clinic Management Dashboard</h1>
        
        <div class="row">
        <div class="col-md-3">
            <div class="metric-card metric-primary mb-4">
                <div class="metric-value"><?php echo esc_html($stats['total_doctors'] ?? 0); ?></div>
                <div class="metric-label">Total Doctors</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card metric-success mb-4" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                <div class="metric-value"><?php echo esc_html($stats['total_patients'] ?? 0); ?></div>
                <div class="metric-label">Total Patients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card metric-success mb-4">
                <div class="metric-value"><?php echo esc_html($stats['todays_appointments'] ?? 0); ?></div>
                <div class="metric-label">Today's Appointments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card metric-warning mb-4" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="metric-value"><?php echo esc_html($stats['upcoming_appointments'] ?? 0); ?></div>
                <div class="metric-label">Upcoming Appointments</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="clinic-glass-card">
                <h4 class="mb-4">Recent Appointments</h4>
                <div class="table-responsive">
                    <table class="clinic-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentAppointments)) : ?>
                                <tr><td colspan="2">No recent appointments.</td></tr>
                            <?php else : ?>
                                <?php foreach ($recentAppointments as $app) : ?>
                                    <tr>
                                        <td><?php echo esc_html($app->appointment_date . ' ' . substr($app->start_time, 0, 5)); ?></td>
                                        <td><span class="clinic-badge clinic-badge-<?php echo $app->status === 'confirmed' ? 'success' : 'warning'; ?>"><?php echo esc_html(ucfirst($app->status)); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="clinic-glass-card">
                <h4 class="mb-4">Recent Patients</h4>
                <div class="table-responsive">
                    <table class="clinic-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPatients)) : ?>
                                <tr><td colspan="2">No recent patients.</td></tr>
                            <?php else : ?>
                                <?php foreach ($recentPatients as $pat) : ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($pat->first_name . ' ' . $pat->last_name); ?></strong></td>
                                        <td><?php echo esc_html($pat->phone); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
