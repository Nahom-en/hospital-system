<?php
require_once '../includes/auth_helper.php';
require_role(1);
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];

// Get patient_id
$stmt = $pdo->prepare("SELECT patient_id, firstname, lastname FROM patient WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
$patient_id = $patient ? $patient['patient_id'] : null;

$error   = '';
$success = '';

// Fetch available doctors (who have at least one schedule entry)
$doctors = $pdo->query("
    SELECT DISTINCT d.doctor_id, d.firstname, d.lastname, d.specialization
    FROM doctors d
    JOIN doctor_schedule ds ON d.doctor_id = ds.doctor_id
    ORDER BY d.lastname, d.firstname
")->fetchAll();

// Get unique specializations for filter
$specializations = [];
foreach ($doctors as $doc) {
    $spec = $doc['specialization'];
    if (!in_array($spec, $specializations)) {
        $specializations[] = $spec;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment']) && $patient_id) {
    $doctor_id = (int)$_POST['doctor_id'];
    $date      = $_POST['appointment_date'];
    $time_slot = $_POST['time_slot'];
    $reason    = trim($_POST['reason']);

    if (empty($doctor_id) || empty($date) || empty($time_slot)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Calculate end_time (30 min appointment)
            $start_dt = new DateTime($time_slot);
            $end_dt   = clone $start_dt;
            $end_dt->modify('+30 minutes');
            $start_time = $start_dt->format('H:i:s');
            $end_time   = $end_dt->format('H:i:s');

            // Check for duplicate booking
            $stmt = $pdo->prepare("
                SELECT appointment_id FROM appointments
                WHERE doctor_id = ? AND appointment_date = ? AND start_time = ? AND status IN ('Scheduled','Confirmed')
            ");
            $stmt->execute([$doctor_id, $date, $start_time]);
            if ($stmt->fetch()) {
                $error = "This time slot is already booked. Please choose another.";
            } else {
                // Insert appointment
                $stmt = $pdo->prepare("
                    INSERT INTO appointments (patient_id, doctor_id, appointment_date, start_time, end_time, reason, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')
                ");
                $stmt->execute([$patient_id, $doctor_id, $date, $start_time, $end_time, $reason]);

                // Notify doctor
                $stmt = $pdo->prepare("SELECT user_id FROM doctors WHERE doctor_id = ?");
                $stmt->execute([$doctor_id]);
                $doctor_user = $stmt->fetch();
                if ($doctor_user) {
                    $patient_name = $patient['firstname'] . ' ' . $patient['lastname'];
                    send_notification($pdo, $doctor_user['user_id'],
                        'New Appointment Request',
                        $patient_name . ' has requested an appointment on ' . date('M j, Y', strtotime($date)) . ' at ' . date('g:i A', strtotime($start_time)) . '.'
                    );
                }

                $success = "Appointment booked successfully! Your request is pending doctor approval.";
            }
        } catch (Exception $e) {
            $error = "Error booking appointment: " . $e->getMessage();
        }
    }
}
?>
<?php
$page_title = 'Book Appointment - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_patient.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="d-flex align-items-center mb-5">
            <button class="mobile-toggle me-3" id="mobile-toggle">
                <i data-lucide="menu"></i>
            </button>
            <h1 class="header-title h2 mb-0">Book an <span class="text-primary">Appointment</span></h1>
        </header>

        <?php if ($success): ?>
        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
            <i data-lucide="check-circle" size="20"></i>
            <div>
                <?= htmlspecialchars($success) ?>
                <a href="./myappointments.php" class="alert-link ms-2">View My Appointments &rarr;</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
            <i data-lucide="alert-circle" size="20"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-8">
                <!-- Booking Stepper -->
                <div class="booking-stepper d-flex justify-content-between mb-5 position-relative">
                    <div class="step-item active text-center" data-step="1">
                        <div class="step-icon mb-2 mx-auto">1</div>
                        <span class="small fw-bold">Doctor</span>
                    </div>
                    <div class="step-item text-center" data-step="2">
                        <div class="step-icon mb-2 mx-auto">2</div>
                        <span class="small fw-bold">Date</span>
                    </div>
                    <div class="step-item text-center" data-step="3">
                        <div class="step-icon mb-2 mx-auto">3</div>
                        <span class="small fw-bold">Time</span>
                    </div>
                    <div class="step-item text-center" data-step="4">
                        <div class="step-icon mb-2 mx-auto">4</div>
                        <span class="small fw-bold">Reason</span>
                    </div>
                    <div class="step-item text-center" data-step="5">
                        <div class="step-icon mb-2 mx-auto">5</div>
                        <span class="small fw-bold">Confirm</span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 p-md-5 overflow-hidden">
                    <form action="" method="POST" id="bookingForm">
                        <!-- Step 1: Choose Doctor -->
                        <div class="step-content active" id="step1">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                Choose Doctor
                            </h5>
                            <div class="mb-4">
                                <label for="doctor_id" class="form-label small fw-bold text-muted text-uppercase">Select Physician</label>
                                <select name="doctor_id" id="doctor_id" class="form-select form-select-lg px-3 border-2" required>
                                    <option value="">Select a doctor</option>
                                    <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc['doctor_id'] ?>" data-spec="<?= htmlspecialchars($doc['specialization']) ?>">
                                        Dr. <?= htmlspecialchars($doc['firstname'] . ' ' . $doc['lastname']) ?> — <?= htmlspecialchars($doc['specialization']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($doctors)): ?>
                                <div class="text-warning small mt-2"><i data-lucide="alert-triangle" size="14" class="me-1"></i> No doctors have set up their schedule yet. Please try again later.</div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Available Specializations</label>
                                <div class="specializations d-flex flex-wrap gap-2">
                                    <?php foreach ($specializations as $spec): ?>
                                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill spec-filter" style="cursor:pointer" data-spec="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mt-5 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary px-5 py-3 fw-bold btn-next" data-next="2">
                                    Next: Choose Date <i data-lucide="arrow-right" class="ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Choose Date -->
                        <div class="step-content d-none" id="step2">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                Choose Date
                            </h5>
                            <div class="mb-4">
                                <label for="appointment_date" class="form-label small fw-bold text-muted text-uppercase">Select Preferred Date</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control form-control-lg border-2" required min="<?= date('Y-m-d') ?>">
                                <div class="form-text mt-2" id="schedule-hint"></div>
                            </div>
                            <div class="mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-light px-4 py-3 fw-bold text-muted btn-prev" data-prev="1">
                                    <i data-lucide="arrow-left" class="me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary px-5 py-3 fw-bold btn-next" data-next="3">
                                    Next: Choose Time <i data-lucide="arrow-right" class="ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Choose Time Slot -->
                        <div class="step-content d-none" id="step3">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                Choose Available Time Slot
                            </h5>
                            <div id="time-slots-container">
                                <p class="text-muted">Select a doctor and date first to see available time slots.</p>
                            </div>
                            <input type="hidden" name="time_slot" id="time_slot_value" value="">
                            <div class="mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-light px-4 py-3 fw-bold text-muted btn-prev" data-prev="2">
                                    <i data-lucide="arrow-left" class="me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary px-5 py-3 fw-bold btn-next" data-next="4">
                                    Next: Write Reason <i data-lucide="arrow-right" class="ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Write Reason -->
                        <div class="step-content d-none" id="step4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                Write Reason
                            </h5>
                            <div class="mb-4">
                                <label for="reason" class="form-label small fw-bold text-muted text-uppercase">Reason for Appointment</label>
                                <textarea name="reason" id="reason" class="form-control border-2 p-3" rows="5" placeholder="Briefly describe your symptoms or reason for visit..."></textarea>
                            </div>
                            <div class="mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-light px-4 py-3 fw-bold text-muted btn-prev" data-prev="3">
                                    <i data-lucide="arrow-left" class="me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary px-5 py-3 fw-bold btn-next" data-next="5">
                                    Next: Confirm <i data-lucide="arrow-right" class="ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 5: Confirm Booking -->
                        <div class="step-content d-none" id="step5">
                            <div class="text-center mb-5">
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                    <i data-lucide="check-circle" size="40"></i>
                                </div>
                                <h4 class="fw-bold">Confirm Booking</h4>
                                <p class="text-muted">Please review your appointment details below.</p>
                            </div>

                            <div class="bg-light p-4 rounded-4 mb-5">
                                <div class="row g-4">
                                    <div class="col-6">
                                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Doctor</label>
                                        <p class="fw-bold mb-0" id="summary-doctor">Not Selected</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Date</label>
                                        <p class="fw-bold mb-0" id="summary-date">Not Selected</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Time</label>
                                        <p class="fw-bold mb-0" id="summary-time">Not Selected</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Reason</label>
                                        <p class="fw-bold mb-0" id="summary-reason">—</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <button type="submit" name="book_appointment" class="btn btn-primary btn-lg py-3 fw-bold">
                                    <i data-lucide="calendar-check" size="20" class="me-2"></i> Confirm Appointment
                                </button>
                                <button type="button" class="btn btn-light py-3 fw-bold text-muted btn-prev" data-prev="4">
                                    Go Back and Edit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        // Schedule data from PHP (doctor_id => {day => {start, end}})
        const scheduleData = <?php
            $sched = [];
            foreach ($doctors as $doc) {
                $stmt2 = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM doctor_schedule WHERE doctor_id = ?");
                $stmt2->execute([$doc['doctor_id']]);
                $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                $sched[$doc['doctor_id']] = [];
                foreach ($rows as $r) {
                    $sched[$doc['doctor_id']][$r['day_of_week']] = [
                        'start' => substr($r['start_time'], 0, 5),
                        'end'   => substr($r['end_time'], 0, 5)
                    ];
                }
            }
            echo json_encode($sched);
        ?>;

        // Booked slots data
        const bookedSlots = <?php
            $booked = [];
            $bStmt = $pdo->query("SELECT doctor_id, appointment_date, start_time FROM appointments WHERE status IN ('Scheduled','Confirmed')");
            while ($row = $bStmt->fetch()) {
                $key = $row['doctor_id'] . '_' . $row['appointment_date'];
                if (!isset($booked[$key])) $booked[$key] = [];
                $booked[$key][] = substr($row['start_time'], 0, 5);
            }
            echo json_encode($booked);
        ?>;

        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        const form = document.getElementById('bookingForm');
        const steps = document.querySelectorAll('.step-content');
        const stepperItems = document.querySelectorAll('.step-item');
        const nextBtns = document.querySelectorAll('.btn-next');
        const prevBtns = document.querySelectorAll('.btn-prev');

        function generateTimeSlots(docId, dateStr) {
            const container = document.getElementById('time-slots-container');
            container.innerHTML = '';

            if (!docId || !dateStr) {
                container.innerHTML = '<p class="text-muted">Select a doctor and date first.</p>';
                return;
            }

            const date = new Date(dateStr + 'T00:00:00');
            const dayName = dayNames[date.getDay()];
            const docSchedule = scheduleData[docId];

            if (!docSchedule || !docSchedule[dayName]) {
                container.innerHTML = '<div class="alert alert-warning border-0"><i data-lucide="alert-triangle" size="16" class="me-2"></i>The doctor is not available on ' + dayName + 's. Please choose another date.</div>';
                lucide.createIcons();
                return;
            }

            const sched = docSchedule[dayName];
            const bookedKey = docId + '_' + dateStr;
            const booked = bookedSlots[bookedKey] || [];

            // Generate 30 min slots
            let slots = [];
            let current = sched.start.split(':').map(Number);
            let end = sched.end.split(':').map(Number);
            let endMinutes = end[0] * 60 + end[1];

            while (current[0] * 60 + current[1] + 30 <= endMinutes) {
                let timeStr = String(current[0]).padStart(2,'0') + ':' + String(current[1]).padStart(2,'0');
                let isBooked = booked.includes(timeStr);

                // Check if slot is in the past (today only)
                let isPast = false;
                const today = new Date().toISOString().split('T')[0];
                if (dateStr === today) {
                    const now = new Date();
                    if (current[0] < now.getHours() || (current[0] === now.getHours() && current[1] <= now.getMinutes())) {
                        isPast = true;
                    }
                }

                slots.push({ time: timeStr, booked: isBooked, past: isPast });

                // Advance 30 min
                current[1] += 30;
                if (current[1] >= 60) {
                    current[0]++;
                    current[1] -= 60;
                }
            }

            if (slots.length === 0) {
                container.innerHTML = '<p class="text-muted">No time slots available for this date.</p>';
                return;
            }

            // Split into morning/afternoon
            let morningHTML = '';
            let afternoonHTML = '';
            slots.forEach((slot, i) => {
                const hour = parseInt(slot.time.split(':')[0]);
                const ampm = hour < 12 ? 'AM' : 'PM';
                const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
                const displayTime = displayHour + ':' + slot.time.split(':')[1] + ' ' + ampm;
                const disabled = (slot.booked || slot.past) ? 'disabled' : '';
                const btnClass = slot.booked ? 'btn-secondary' : (slot.past ? 'btn-light text-muted' : 'btn-outline-primary');
                const label = slot.booked ? displayTime + ' (Booked)' : (slot.past ? displayTime + ' (Past)' : displayTime);

                const html = `
                    <input type="radio" class="btn-check time-slot-radio" name="time_slot_radio" id="slot_${i}" value="${slot.time}" ${disabled}>
                    <label class="btn ${btnClass} px-4 py-2 rounded-4 fw-bold" for="slot_${i}">${label}</label>
                `;

                if (hour < 12) morningHTML += html;
                else afternoonHTML += html;
            });

            let html = '';
            if (morningHTML) {
                html += '<label class="form-label small fw-bold text-muted text-uppercase mb-3">Morning Slots</label>';
                html += '<div class="d-flex flex-wrap gap-3 mb-4">' + morningHTML + '</div>';
            }
            if (afternoonHTML) {
                html += '<label class="form-label small fw-bold text-muted text-uppercase mb-3">Afternoon Slots</label>';
                html += '<div class="d-flex flex-wrap gap-3">' + afternoonHTML + '</div>';
            }
            container.innerHTML = html;

            // Bind radio click to hidden input
            container.querySelectorAll('.time-slot-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('time_slot_value').value = this.value;
                });
            });
        }

        function updateSummary() {
            const docSelect = document.getElementById('doctor_id');
            document.getElementById('summary-doctor').innerText = docSelect.options[docSelect.selectedIndex].text || 'Not Selected';

            const dateVal = document.getElementById('appointment_date').value;
            document.getElementById('summary-date').innerText = dateVal ? new Date(dateVal + 'T00:00:00').toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'}) : 'Not Selected';

            const timeVal = document.getElementById('time_slot_value').value;
            if (timeVal) {
                const [h, m] = timeVal.split(':').map(Number);
                const ampm = h < 12 ? 'AM' : 'PM';
                const dh = h > 12 ? h - 12 : (h === 0 ? 12 : h);
                document.getElementById('summary-time').innerText = dh + ':' + String(m).padStart(2,'0') + ' ' + ampm;
            } else {
                document.getElementById('summary-time').innerText = 'Not Selected';
            }

            document.getElementById('summary-reason').innerText = document.getElementById('reason').value || '—';
        }

        // When date changes, regenerate time slots
        document.getElementById('appointment_date').addEventListener('change', function() {
            const docId = document.getElementById('doctor_id').value;
            generateTimeSlots(docId, this.value);

            // Show day hint
            if (this.value) {
                const date = new Date(this.value + 'T00:00:00');
                const dayName = dayNames[date.getDay()];
                document.getElementById('schedule-hint').innerText = 'Selected day: ' + dayName;
            }
        });

        // Specialization filter
        document.querySelectorAll('.spec-filter').forEach(badge => {
            badge.addEventListener('click', function() {
                const spec = this.dataset.spec;
                const select = document.getElementById('doctor_id');
                // Filter options
                Array.from(select.options).forEach(opt => {
                    if (opt.value === '') return;
                    opt.style.display = (opt.dataset.spec === spec || !spec) ? '' : 'none';
                });
                // Visual feedback
                document.querySelectorAll('.spec-filter').forEach(b => b.classList.remove('bg-primary', 'text-white'));
                this.classList.add('bg-primary', 'text-white');
            });
        });

        nextBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const nextStep = btn.getAttribute('data-next');
                const currentStepDiv = btn.closest('.step-content');

                // Validation
                if (nextStep == 2 && !document.getElementById('doctor_id').value) {
                    alert('Please select a doctor.');
                    return;
                }
                if (nextStep == 3) {
                    const dateVal = document.getElementById('appointment_date').value;
                    if (!dateVal) {
                        alert('Please select a date.');
                        return;
                    }
                    // Generate time slots when moving to step 3
                    generateTimeSlots(document.getElementById('doctor_id').value, dateVal);
                }
                if (nextStep == 4 && !document.getElementById('time_slot_value').value) {
                    alert('Please select a time slot.');
                    return;
                }
                if (nextStep == 5) updateSummary();

                steps.forEach(s => s.classList.add('d-none'));
                document.getElementById('step' + nextStep).classList.remove('d-none');

                stepperItems.forEach(item => {
                    const stepNum = item.getAttribute('data-step');
                    if (stepNum <= nextStep) item.classList.add('active');
                    else item.classList.remove('active');
                });

                lucide.createIcons();
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const prevStep = btn.getAttribute('data-prev');
                steps.forEach(s => s.classList.add('d-none'));
                document.getElementById('step' + prevStep).classList.remove('d-none');

                stepperItems.forEach(item => {
                    const stepNum = item.getAttribute('data-step');
                    if (stepNum <= prevStep) item.classList.add('active');
                    else item.classList.remove('active');
                });

                lucide.createIcons();
            });
        });

<?php require_once '../includes/footer.php'; ?>
