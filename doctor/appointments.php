<?php
require_once '../includes/auth_helper.php';
require_role(2); // 2 is Doctor
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];

// Get doctor info
$stmt = $pdo->prepare("SELECT doctor_id, firstname, lastname, specialization FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
$doctor_id   = $doctor ? $doctor['doctor_id'] : null;
$doctor_name = $doctor ? 'Dr. ' . $doctor['firstname'] . ' ' . $doctor['lastname'] : 'Doctor';

$message = '';
$message_type = '';

// Handle action (Approve / Reject / Complete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $doctor_id) {
    $appt_id = (int)$_POST['appointment_id'];
    $action  = $_POST['action'];

    // Verify appointment belongs to this doctor
    $stmt = $pdo->prepare("
        SELECT a.*, p.user_id AS patient_user_id, pa.firstname AS p_first, pa.lastname AS p_last
        FROM appointments a
        JOIN patient pa ON a.patient_id = pa.patient_id
        JOIN users p ON pa.user_id = p.user_id
        WHERE a.appointment_id = ? AND a.doctor_id = ?
    ");
    $stmt->execute([$appt_id, $doctor_id]);
    $appt = $stmt->fetch();

    if ($appt) {
        $new_status = '';
        $notif_title = '';
        $notif_msg   = '';

        if ($action === 'approve' && $appt['status'] === 'Scheduled') {
            $new_status  = 'Confirmed';
            $notif_title = 'Appointment Confirmed';
            $notif_msg   = "Your appointment on " . date('M j, Y', strtotime($appt['appointment_date'])) . " has been confirmed by " . $doctor_name . ".";
        } elseif ($action === 'complete' && $appt['status'] === 'Confirmed') {
            $new_status  = 'Completed';
            $notif_title = 'Appointment Completed';
            $notif_msg   = "Your appointment on " . date('M j, Y', strtotime($appt['appointment_date'])) . " has been marked as completed.";
        } elseif ($action === 'reject' && in_array($appt['status'], ['Scheduled', 'Confirmed'])) {
            $new_status  = 'Cancelled';
            $notif_title = 'Appointment Cancelled';
            $notif_msg   = "Your appointment on " . date('M j, Y', strtotime($appt['appointment_date'])) . " has been cancelled by the doctor.";
        }

        if ($new_status) {
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
            $stmt->execute([$new_status, $appt_id]);

            // Notify patient
            send_notification($pdo, $appt['patient_user_id'], $notif_title, $notif_msg);

            $message = "Appointment status updated to {$new_status}.";
            $message_type = 'success';
        }
    }
}

// Fetch all appointments for this doctor
$appointments = [];
if ($doctor_id) {
    $stmt = $pdo->prepare("
        SELECT a.*, pa.firstname AS p_first, pa.lastname AS p_last
        FROM appointments a
        JOIN patient pa ON a.patient_id = pa.patient_id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date DESC, a.start_time DESC
    ");
    $stmt->execute([$doctor_id]);
    $appointments = $stmt->fetchAll();
}

function status_badge($status) {
    $map = [
        'Scheduled'  => 'bg-warning text-dark',
        'Confirmed'  => 'bg-success',
        'Completed'  => 'bg-primary',
        'Cancelled'  => 'bg-secondary',
        'No-show'    => 'bg-danger',
    ];
    $cls = $map[$status] ?? 'bg-secondary';
    return "<span class=\"badge {$cls} px-3 py-2 rounded-pill\">{$status}</span>";
}
?>
<?php
$page_title = 'Doctor Appointments - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_doctor.php';
?>

    <main class="main-content">
        <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5 gap-4">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <h1 class="header-title h2 mb-0">Manage <span class="text-primary">Appointments</span></h1>
            </div>
            <div class="search-box position-relative" style="min-width: 300px;">
                <i data-lucide="search" class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" size="18"></i>
                <input type="text" id="search-input" class="form-control ps-5 py-2 rounded-pill border-2" placeholder="Search by patient name or status...">
            </div>
        </header>

        <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show border-0 rounded-3 mb-4">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (!$doctor_id): ?>
        <div class="alert alert-warning border-0 rounded-3">
            Doctor profile not found. Please contact the administrator.
        </div>
        <?php else: ?>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="appointments-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Patient</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Time</th>
                            <th class="py-3">Reason</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i data-lucide="calendar-x" size="40" class="mb-3 d-block mx-auto"></i>
                                No appointments yet.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= htmlspecialchars($appt['p_first'] . ' ' . $appt['p_last']) ?></td>
                            <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($appt['start_time'])) ?></td>
                            <td><span class="text-muted small"><?= htmlspecialchars($appt['reason'] ?: '—') ?></span></td>
                            <td><?= status_badge($appt['status']) ?></td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if ($appt['status'] === 'Scheduled'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-outline-success" title="Approve">
                                                <i data-lucide="check" size="16"></i>
                                            </button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Reject this appointment?')">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" title="Reject">
                                                <i data-lucide="x" size="16"></i>
                                            </button>
                                        </form>
                                    <?php elseif ($appt['status'] === 'Confirmed'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                                            <button type="submit" name="action" value="complete" class="btn btn-sm btn-outline-primary" title="Mark Complete">
                                                <i data-lucide="check-circle" size="16"></i>
                                            </button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Cancel this appointment?')">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" title="Cancel">
                                                <i data-lucide="x" size="16"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>

<script>
    // Live search
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#appointments-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
</script>
<?php require_once '../includes/footer.php'; ?>
