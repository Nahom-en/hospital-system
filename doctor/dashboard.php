<?php
require_once '../includes/auth_helper.php';
require_role(2); // 2 is Doctor
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Get doctor info
$stmt = $pdo->prepare("SELECT doctor_id, firstname, lastname, specialization FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
$doctor_id   = $doctor ? $doctor['doctor_id'] : null;
$doctor_name = $doctor ? 'Dr. ' . $doctor['firstname'] . ' ' . $doctor['lastname'] : 'Doctor';

// --- Real-time stats ---
$today_appointments = 0;
$pending_requests   = 0;
$completed_today    = 0;
$total_patients     = 0;
$today_schedule     = [];
$upcoming_followups = [];

if ($doctor_id) {
    // Today's appointments count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = CURDATE()");
    $stmt->execute([$doctor_id]);
    $today_appointments = $stmt->fetchColumn();

    // Pending requests
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'Scheduled'");
    $stmt->execute([$doctor_id]);
    $pending_requests = $stmt->fetchColumn();

    // Completed today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = CURDATE() AND status = 'Completed'");
    $stmt->execute([$doctor_id]);
    $completed_today = $stmt->fetchColumn();

    // Total unique patients
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ?");
    $stmt->execute([$doctor_id]);
    $total_patients = $stmt->fetchColumn();

    // Today's schedule
    $stmt = $pdo->prepare("
        SELECT a.appointment_id, a.start_time, a.reason, a.status,
               pa.firstname, pa.lastname
        FROM appointments a
        JOIN patient pa ON a.patient_id = pa.patient_id
        WHERE a.doctor_id = ? AND a.appointment_date = CURDATE()
        ORDER BY a.start_time ASC
    ");
    $stmt->execute([$doctor_id]);
    $today_schedule = $stmt->fetchAll();

    // Upcoming follow-ups (next 7 days, not today)
    $stmt = $pdo->prepare("
        SELECT a.appointment_id, a.appointment_date, a.start_time, a.reason, a.status,
               pa.firstname, pa.lastname
        FROM appointments a
        JOIN patient pa ON a.patient_id = pa.patient_id
        WHERE a.doctor_id = ? AND a.appointment_date > CURDATE()
          AND a.appointment_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
          AND a.status IN ('Scheduled','Confirmed')
        ORDER BY a.appointment_date ASC, a.start_time ASC
        LIMIT 5
    ");
    $stmt->execute([$doctor_id]);
    $upcoming_followups = $stmt->fetchAll();
}

function status_badge($status) {
    $map = [
        'Scheduled'  => 'bg-warning text-dark',
        'Confirmed'  => 'bg-success',
        'Completed'  => 'bg-primary',
        'Cancelled'  => 'bg-secondary',
        'No-show'    => 'bg-danger',
    ];
    return '<span class="badge ' . ($map[$status] ?? 'bg-secondary') . ' px-3 py-2 rounded-pill">' . $status . '</span>';
}
?>
<?php
$page_title = 'Doctor Dashboard - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_doctor.php';
?>

    <main class="main-content">
        <!-- Welcome Section -->
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" id="time-greeting">Welcome</p>
                    <h1 class="header-title h2 mb-0 fw-bold">Welcome, <span class="text-primary"><?= htmlspecialchars($doctor_name) ?></span></h1>
                    <?php if ($doctor): ?>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($doctor['specialization']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="./appointments.php" class="btn btn-primary px-4 py-2 fw-bold d-none d-md-flex align-items-center gap-2 shadow-sm rounded-3">
                <i data-lucide="calendar" size="18"></i>
                Manage Appointments
            </a>
        </header>

        <!-- Stat Cards Section -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i data-lucide="calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Today's Appointments</p>
                            <h3 class="fw-bold mb-0"><?= $today_appointments ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                            <i data-lucide="calendar-clock"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Pending Requests</p>
                            <h3 class="fw-bold mb-0"><?= $pending_requests ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success-subtle text-success p-3 rounded-circle">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Completed Today</p>
                            <h3 class="fw-bold mb-0"><?= $completed_today ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info-subtle text-info p-3 rounded-circle">
                            <i data-lucide="users"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Total Patients</p>
                            <h3 class="fw-bold mb-0"><?= $total_patients ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Today's Schedule Table -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-3">Today's Schedule</h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"><?= date('l, M j, Y') ?></span>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Patient Name</th>
                                        <th class="py-3">Time</th>
                                        <th class="py-3">Reason</th>
                                        <th class="py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($today_schedule)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i data-lucide="calendar-check" size="36" class="mb-3 d-block mx-auto"></i>
                                            No appointments scheduled for today.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($today_schedule as $appt): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= htmlspecialchars($appt['firstname'] . ' ' . $appt['lastname']) ?></td>
                                        <td><?= date('g:i A', strtotime($appt['start_time'])) ?></td>
                                        <td><span class="text-muted small"><?= htmlspecialchars($appt['reason'] ?: '—') ?></span></td>
                                        <td><?= status_badge($appt['status']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pb-4 px-4">
                        <a href="./appointments.php" class="btn btn-outline-primary btn-sm fw-bold">Manage All Appointments &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Follow-ups Sidebar -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold mb-0">Upcoming (Next 7 Days)</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <?php if (empty($upcoming_followups)): ?>
                        <div class="text-center py-4 text-muted">
                            <i data-lucide="calendar" size="36" class="mb-3 d-block mx-auto"></i>
                            No upcoming appointments this week.
                        </div>
                        <?php else: ?>
                        <?php foreach ($upcoming_followups as $f): ?>
                        <div class="alert alert-light border d-flex align-items-start gap-3 mb-3 p-3">
                            <i data-lucide="calendar" class="text-primary mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="fw-bold mb-0 small"><?= htmlspecialchars($f['firstname'] . ' ' . $f['lastname']) ?></p>
                                <p class="text-muted small mb-0"><?= date('D, M j', strtotime($f['appointment_date'])) ?> &mdash; <?= date('g:i A', strtotime($f['start_time'])) ?></p>
                                <?php if ($f['reason']): ?>
                                <p class="text-muted small mb-0"><em><?= htmlspecialchars($f['reason']) ?></em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-top-0 pb-4 px-4">
                        <a href="./schedule.php" class="btn btn-outline-secondary btn-sm fw-bold w-100">Manage Schedule &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    const greetingEl = document.getElementById('time-greeting');
    if (greetingEl) {
        const hour = new Date().getHours();
        if (hour < 12) greetingEl.innerText = "Good Morning";
        else if (hour < 17) greetingEl.innerText = "Good Afternoon";
        else greetingEl.innerText = "Good Evening";
    }
</script>
<?php require_once '../includes/footer.php'; ?>
