<?php
require_once '../includes/auth_helper.php';
require_role(1);
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];

// Get patient_id
$stmt = $pdo->prepare("SELECT patient_id FROM patient WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
$patient_id = $patient ? $patient['patient_id'] : null;

$message = '';
$message_type = '';

// Handle Cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment']) && $patient_id) {
    $appt_id = (int)$_POST['appointment_id'];
    try {
        // Verify ownership
        $stmt = $pdo->prepare("SELECT a.*, d.user_id as doctor_user_id FROM appointments a JOIN doctors d ON a.doctor_id = d.doctor_id WHERE a.appointment_id = ? AND a.patient_id = ?");
        $stmt->execute([$appt_id, $patient_id]);
        $appt = $stmt->fetch();

        if ($appt && in_array($appt['status'], ['Scheduled', 'Confirmed'])) {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND patient_id = ?");
            $stmt->execute([$appt_id, $patient_id]);

            // Notify the doctor
            send_notification($pdo, $appt['doctor_user_id'],
                'Appointment Cancelled',
                'A patient has cancelled their appointment scheduled for ' . date('M j, Y', strtotime($appt['appointment_date'])) . '.'
            );

            $message = 'Appointment cancelled successfully.';
            $message_type = 'success';
        } else {
            $message = 'Unable to cancel this appointment.';
            $message_type = 'danger';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Fetch appointments
$appointments = [];
if ($patient_id) {
    $stmt = $pdo->prepare("
        SELECT a.*, d.firstname, d.lastname, d.specialization
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC, a.start_time DESC
    ");
    $stmt->execute([$patient_id]);
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
$page_title = 'My Appointments - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_patient.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5 gap-4">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <h1 class="header-title h2 mb-0">My <span class="text-primary">Appointments</span></h1>
            </div>
            
            <div class="search-box position-relative" style="min-width: 300px;">
                <i data-lucide="search" class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" size="18"></i>
                <input type="text" id="search-input" class="form-control ps-5 py-2 rounded-pill border-2" placeholder="Search by doctor or status...">
            </div>
        </header>

        <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="appointments-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Doctor</th>
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
                                No appointments found. <a href="./bookappointment.php">Book one now!</a>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td class="ps-4 fw-bold">Dr. <?= htmlspecialchars($appt['firstname'] . ' ' . $appt['lastname']) ?><br>
                                <small class="text-muted fw-normal"><?= htmlspecialchars($appt['specialization']) ?></small>
                            </td>
                            <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($appt['start_time'])) ?></td>
                            <td><span class="text-muted small"><?= htmlspecialchars($appt['reason'] ?: '—') ?></span></td>
                            <td><?= status_badge($appt['status']) ?></td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if (in_array($appt['status'], ['Scheduled', 'Confirmed'])): ?>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                        <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                                        <button type="submit" name="cancel_appointment" class="btn btn-sm btn-outline-danger" title="Cancel">
                                            <i data-lucide="x" size="16"></i> Cancel
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
    </main>

<script>
    // Live search
    const searchInput = document.getElementById('search-input');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#appointments-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
</script>
<?php require_once '../includes/footer.php'; ?>
