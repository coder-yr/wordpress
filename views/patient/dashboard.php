<div class="clinic-modern-wrapper">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <div class="container-fluid">
        <ul class="nav nav-pills" id="patientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">My Appointments</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="book-tab" data-bs-toggle="tab" data-bs-target="#book" type="button" role="tab">Book Appointment</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">Medical Reports</button>
            </li>
        </ul>
        <div class="tab-content" id="patientTabsContent">
            <!-- Appointments -->
            <div class="tab-pane fade show active clinic-glass-card" id="appointments" role="tabpanel">
                <h4>Upcoming Appointments</h4>
                <div id="appointments-list">
                    <?php if (empty($appointments)) : ?>
                        <div class="alert alert-info">You have no upcoming appointments.</div>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="clinic-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Meeting Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $apt) : ?>
                                        <tr>
                                            <td><?php echo esc_html($apt->appointment_date); ?></td>
                                            <td><?php echo esc_html(substr($apt->start_time, 0, 5)); ?></td>
                                            <td><?php echo esc_html($apt->doctor_name); ?></td>
                                            <td><?php echo esc_html($apt->department_name ?: 'General'); ?></td>
                                            <td><span class="clinic-badge clinic-badge-<?php echo $apt->status === 'confirmed' ? 'success' : 'warning'; ?>"><?php echo esc_html(ucfirst($apt->status)); ?></span></td>
                                            <td>
                                                <?php if ($apt->status === 'confirmed' && $apt->meeting_link) : ?>
                                                    <a href="<?php echo esc_url($apt->meeting_link); ?>" target="_blank" class="btn btn-sm btn-clinic-primary">Join Room</a>
                                                <?php else : ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Book -->
            <div class="tab-pane fade clinic-glass-card" id="book" role="tabpanel">
                <h4>Book a New Appointment</h4>
                <form id="bookAppointmentForm" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">Select Department</label>
                        <select class="form-select" id="departmentSelect">
                            <option value="1">General Medicine</option>
                            <option value="2">Cardiology</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Doctor</label>
                        <select class="form-select" id="doctorSelect"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="appointmentDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available Slots</label>
                        <select class="form-select" id="slotSelect" disabled>
                            <option>Select a date first</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-clinic-primary" id="btnBook">Confirm Booking</button>
                </form>
            </div>

            <!-- Reports -->
            <div class="tab-pane fade clinic-glass-card" id="reports" role="tabpanel">
                <h4>My Reports</h4>
                <p>No reports uploaded yet.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Example JS to interact with our API
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch Doctors and populate #doctorSelect
            fetch('/wp-json/clinic/v1/doctors')
                .then(res => res.json())
                .then(data => {
                    const doctorSelect = document.getElementById('doctorSelect');
                    const deptSelect = document.getElementById('departmentSelect');
                    
                    doctorSelect.innerHTML = '<option value="">-- Select a Doctor --</option>';
                    if(data && data.data && Array.isArray(data.data)) {
                        window.allDoctors = data.data;
                        
                        // Populate unique departments from doctors
                        const depts = new Map();
                        data.data.forEach(doc => {
                            if(!depts.has(doc.department_id)) {
                                depts.set(doc.department_id, `Department ${doc.department_id}`);
                            }
                        });
                        
                        deptSelect.innerHTML = '<option value="">-- All Departments --</option>';
                        depts.forEach((name, id) => {
                            deptSelect.innerHTML += `<option value="${id}">${name}</option>`;
                        });

                        const renderDoctors = (deptId) => {
                            doctorSelect.innerHTML = '<option value="">-- Select a Doctor --</option>';
                            data.data.forEach(doc => {
                                if(!deptId || doc.department_id == deptId) {
                                    doctorSelect.innerHTML += `<option value="${doc.id}">${doc.first_name} ${doc.last_name} (${doc.specialization})</option>`;
                                }
                            });
                        };
                        
                        renderDoctors('');
                        
                        deptSelect.addEventListener('change', (e) => {
                            renderDoctors(e.target.value);
                        });
                    }
                });

            // Listen for date change to fetch slots
            document.getElementById('appointmentDate').addEventListener('change', function(e) {
                const docId = document.getElementById('doctorSelect').value;
                const date = e.target.value;
                
                fetch(`/wp-json/clinic/v1/doctors/${docId}/availability?date=${date}`)
                    .then(res => res.json())
                    .then(data => {
                        const slotSelect = document.getElementById('slotSelect');
                        slotSelect.innerHTML = '';
                        slotSelect.disabled = false;
                        if(data.data && data.data.available_slots) {
                            data.data.available_slots.forEach(slot => {
                                slotSelect.innerHTML += `<option value="${slot}">${slot}</option>`;
                            });
                        }
                    });
            });

            // Handle booking submission
            document.getElementById('bookAppointmentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const doctorId = document.getElementById('doctorSelect').value;
                const date = document.getElementById('appointmentDate').value;
                const timeStr = document.getElementById('slotSelect').value;
                const deptId = document.getElementById('departmentSelect').value;
                
                if(!doctorId || !date || !timeStr || timeStr === 'Select a date first') {
                    alert('Please fill all required fields');
                    return;
                }

                // parse times
                const [start_time, end_time] = timeStr.split(' - ');
                
                const payload = {
                    doctor_id: doctorId,
                    department_id: deptId || 1, // Fallback if no department selected
                    appointment_date: date,
                    start_time: start_time,
                    end_time: end_time || start_time,
                    reason: 'General Consultation'
                };

                const btn = document.getElementById('btnBook');
                btn.disabled = true;
                btn.innerText = 'Booking...';

                fetch('/wp-json/clinic/v1/appointments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerText = 'Confirm Booking';
                    if(data.status === 'success') {
                        alert('Appointment booked successfully!');
                        // Optional: Reset form or switch tabs
                    } else {
                        alert('Error booking appointment: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerText = 'Confirm Booking';
                    alert('A network error occurred.');
                });
            });
        });
    </script>
</div>
