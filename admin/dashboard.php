<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

// --- Real-time stats ---
// Total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Active sessions approximation: users who have appointments in last 30 days
$stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM users WHERE created_at >= NOW() - INTERVAL 30 DAY");
$active_sessions = $stmt->fetchColumn();

// DB status check
$db_status = 'Healthy';
$db_status_class = 'text-success';
try {
    $pdo->query("SELECT 1");
} catch (Exception $e) {
    $db_status = 'Error';
    $db_status_class = 'text-danger';
}

// Extra stats
$stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
$total_doctors = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM patient");
$total_patients = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()");
$today_appointments = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Scheduled'");
$pending_appointments = $stmt->fetchColumn();

// Recent appointments
$recent_appts = $pdo->query("
    SELECT a.appointment_id, a.appointment_date, a.start_time, a.status,
           p.firstname AS p_first, p.lastname AS p_last,
           d.firstname AS d_first, d.lastname AS d_last
    FROM appointments a
    JOIN patient p ON a.patient_id = p.patient_id
    JOIN doctors d ON a.doctor_id = d.doctor_id
    ORDER BY a.created_at DESC
    LIMIT 8
")->fetchAll();
?>
<?php
$page_title = 'Admin Dashboard - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_admin.php';
?>

    <main class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Administrator Control Panel</p>
                    <h1 class="header-title h2 mb-0 fw-bold">System <span class="text-primary">Overview</span></h1>
                </div>
            </div>
        </header>

        <!-- Primary Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="card p-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i data-lucide="users"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Total Users</p>
                            <h3 class="fw-bold mb-0"><?= number_format($total_users) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card p-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success-subtle text-success p-3 rounded-circle">
                            <i data-lucide="activity"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">New Users (30 days)</p>
                            <h3 class="fw-bold mb-0"><?= number_format($active_sessions) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card p-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                            <i data-lucide="database"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">DB Status</p>
                            <h3 class="fw-bold mb-0 <?= $db_status_class ?>"><?= $db_status ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <div class="card p-4 shadow-sm border-0 text-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle d-inline-flex mb-3 mx-auto">
                        <i data-lucide="stethoscope"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= $total_doctors ?></h4>
                    <p class="text-muted small mb-0">Doctors</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 shadow-sm border-0 text-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-flex mb-3 mx-auto">
                        <i data-lucide="heart-pulse"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= $total_patients ?></h4>
                    <p class="text-muted small mb-0">Patients</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 shadow-sm border-0 text-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle d-inline-flex mb-3 mx-auto">
                        <i data-lucide="calendar-check"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= $today_appointments ?></h4>
                    <p class="text-muted small mb-0">Today's Appts</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 shadow-sm border-0 text-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle d-inline-flex mb-3 mx-auto">
                        <i data-lucide="clock"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= $pending_appointments ?></h4>
                    <p class="text-muted small mb-0">Pending</p>
                </div>
            </div>
        </div>

        <!-- Recent Appointments Table -->
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-3">Recent Appointments</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Patient</th>
                            <th class="py-3">Doctor</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Time</th>
                            <th class="py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_appts)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No appointments yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recent_appts as $a): ?>
                        <?php
                            $badge_map = [
                                'Scheduled'  => 'bg-warning text-dark',
                                'Confirmed'  => 'bg-success',
                                'Completed'  => 'bg-primary',
                                'Cancelled'  => 'bg-secondary',
                                'No-show'    => 'bg-danger',
                            ];
                            $badge = $badge_map[$a['status']] ?? 'bg-secondary';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= htmlspecialchars($a['p_first'] . ' ' . $a['p_last']) ?></td>
                            <td>Dr. <?= htmlspecialchars($a['d_first'] . ' ' . $a['d_last']) ?></td>
                            <td><?= date('M j, Y', strtotime($a['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($a['start_time'])) ?></td>
                            <td><span class="badge <?= $badge ?> px-3 py-2 rounded-pill"><?= $a['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0 pb-4 px-4">
                <a href="./all_appointments.php" class="btn btn-outline-primary btn-sm fw-bold">View All Appointments &rarr;</a>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
