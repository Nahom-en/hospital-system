<?php
require_once '../includes/auth_helper.php';
require_role(1); // 1 is Patient
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];
$notifications = get_notifications($pdo, $user_id, 3);

// Fetch patient details
$stmt = $pdo->prepare("SELECT patient_id, firstname FROM patient WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
$first_name = $patient ? $patient['firstname'] : 'Patient';
$patient_id  = $patient ? $patient['patient_id'] : null;

// --- Real-time appointment stats ---
$stats = ['upcoming' => 0, 'completed' => 0, 'pending' => 0, 'cancelled' => 0];
$next_appointment = null;

if ($patient_id) {
    // Upcoming (Confirmed + future date)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'Confirmed' AND appointment_date >= CURDATE()");
    $stmt->execute([$patient_id]);
    $stats['upcoming'] = $stmt->fetchColumn();

    // Completed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'Completed'");
    $stmt->execute([$patient_id]);
    $stats['completed'] = $stmt->fetchColumn();

    // Pending (Scheduled, not yet confirmed)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'Scheduled'");
    $stmt->execute([$patient_id]);
    $stats['pending'] = $stmt->fetchColumn();

    // Cancelled
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'Cancelled'");
    $stmt->execute([$patient_id]);
    $stats['cancelled'] = $stmt->fetchColumn();

    // Next upcoming appointment
    $stmt = $pdo->prepare("
        SELECT a.*, d.firstname, d.lastname, d.specialization
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE a.patient_id = ? AND a.appointment_date >= CURDATE()
          AND a.status IN ('Scheduled','Confirmed')
        ORDER BY a.appointment_date ASC, a.start_time ASC
        LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $next_appointment = $stmt->fetch();
}
?>
<?php
$page_title = 'Patient Dashboard - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_patient.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" id="time-greeting">Welcome</p>
                    <h1 class="header-title h2 mb-0 fw-bold">Welcome, <span class="text-primary"><?= htmlspecialchars($first_name) ?></span></h1>
                </div>
            </div>
            <a href="./bookappointment.php" class="btn btn-primary px-4 py-2 fw-bold d-none d-md-flex align-items-center gap-2 shadow-sm rounded-3">
                <i data-lucide="calendar-plus" size="18"></i>
                Book Appointment
            </a>
        </header>

        <div class="d-md-none mb-4">
            <a href="./bookappointment.php" class="btn btn-primary w-100 py-3 fw-bold shadow-sm rounded-3">
                <i data-lucide="calendar-plus" size="18" class="me-2"></i>
                Book Appointment
            </a>
        </div>

        <!-- Stat Cards Section -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-primary">
                        <i data-lucide="clock"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Upcoming Appointments</h3>
                    <p class="h2 fw-bold mb-0"><?= $stats['upcoming'] ?></p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-success">
                        <i data-lucide="check-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Completed Appointments</h3>
                    <p class="h2 fw-bold mb-0"><?= $stats['completed'] ?></p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-warning">
                        <i data-lucide="alert-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Pending Appointments</h3>
                    <p class="h2 fw-bold mb-0"><?= $stats['pending'] ?></p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-danger">
                        <i data-lucide="x-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Cancelled Appointments</h3>
                    <p class="h2 fw-bold mb-0"><?= $stats['cancelled'] ?></p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Next Appointment Card -->
            <div class="col-12 col-xl-8">
                <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <i data-lucide="calendar" class="text-primary"></i>
                    Next Appointment
                </h4>
                <div class="card border-0 shadow-sm overflow-hidden">
                    <?php if ($next_appointment): ?>
                    <div class="card-header bg-primary py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-white fw-bold mb-0">Upcoming Visit</h5>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold"><?= htmlspecialchars($next_appointment['status']) ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                                        <i data-lucide="user-round" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small fw-semibold text-uppercase mb-1">Doctor Name</p>
                                        <p class="h6 fw-bold mb-0">Dr. <?= htmlspecialchars($next_appointment['firstname'] . ' ' . $next_appointment['lastname']) ?></p>
                                        <p class="text-muted small mb-0"><?= htmlspecialchars($next_appointment['specialization']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                                        <i data-lucide="clock-3" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small fw-semibold text-uppercase mb-1">Date &amp; Time</p>
                                        <p class="h6 fw-bold mb-0"><?= date('M j, Y', strtotime($next_appointment['appointment_date'])) ?> &mdash; <?= date('g:i A', strtotime($next_appointment['start_time'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($next_appointment['reason'])): ?>
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-4 mt-2">
                                    <p class="text-muted small fw-semibold text-uppercase mb-2"><i data-lucide="sticky-note" size="14" class="me-1"></i> Reason</p>
                                    <p class="mb-0 small text-dark"><?= htmlspecialchars($next_appointment['reason']) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <a href="./myappointments.php" class="btn btn-primary px-4">View All Appointments</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card-header bg-light py-3 px-4">
                        <h5 class="text-muted fw-bold mb-0">No Upcoming Appointments</h5>
                    </div>
                    <div class="card-body p-4 text-center py-5">
                        <i data-lucide="calendar-x" size="48" class="text-muted mb-3"></i>
                        <p class="text-muted">You have no upcoming appointments scheduled.</p>
                        <a href="./bookappointment.php" class="btn btn-primary mt-2">Book an Appointment</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="col-12 col-xl-4">
                <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <i data-lucide="bell" class="text-primary"></i>
                    Recent Notifications
                </h4>
                <div class="card border-0 shadow-sm p-4">
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notif): ?>
                                <div class="d-flex gap-3 pb-3 border-bottom <?= !$notif['is_read'] ? 'bg-light' : '' ?>">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-circle flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="bell" size="18"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($notif['title']) ?></h6>
                                        <p class="text-muted small mb-1"><?= htmlspecialchars($notif['message']) ?></p>
                                        <span class="text-muted" style="font-size: 10px;"><?= date('M j, g:i A', strtotime($notif['created_at'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small text-center py-3">No recent notifications.</div>
                        <?php endif; ?>
                    </div>
                    <a href="notifications.php" class="btn btn-light w-100 mt-4 fw-bold text-muted small">View All Notifications</a>
                </div>
            </div>
        </div>
    </main>

<script>
    const greetingEl = document.getElementById('time-greeting');
    const hour = new Date().getHours();
    if (hour < 12) greetingEl.innerText = "Good Morning";
    else if (hour < 17) greetingEl.innerText = "Good Afternoon";
    else greetingEl.innerText = "Good Evening";
</script>
<?php require_once '../includes/footer.php'; ?>
