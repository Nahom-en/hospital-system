<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

// Fetch all appointments
try {
    $stmt = $pdo->query("
        SELECT a.appointment_id, a.appointment_date, a.start_time, a.status, a.reason,
               p.firstname AS patient_fname, p.lastname AS patient_lname,
               d.specialization, u_doc.email AS doc_email
        FROM appointments a
        JOIN patient p ON a.patient_id = p.patient_id
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u_doc ON d.user_id = u_doc.user_id
        ORDER BY a.appointment_date DESC, a.start_time DESC
    ");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $appointments = [];
    $error = "Failed to load appointments.";
}
?>
<?php
$page_title = 'All Appointments - Admin Dashboard';
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
                    <h1 class="header-title h2 mb-0 fw-bold">All <span class="text-primary">Appointments</span></h1>
                </div>
            </div>
        </header>

        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase small fw-bold text-muted py-3">ID</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Date & Time</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Patient</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Doctor</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Reason</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($appointments) > 0): ?>
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $apt['appointment_id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= date('M j, Y', strtotime($apt['appointment_date'])) ?></div>
                                        <div class="small text-muted"><?= date('h:i A', strtotime($apt['start_time'])) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($apt['patient_fname'] . ' ' . $apt['patient_lname']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($apt['doc_email']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($apt['specialization']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($apt['reason']) ?></td>
                                    <td>
                                        <?php
                                        $badge = 'bg-secondary';
                                        if ($apt['status'] == 'Confirmed') $badge = 'bg-success';
                                        if ($apt['status'] == 'Completed') $badge = 'bg-primary';
                                        if ($apt['status'] == 'Cancelled') $badge = 'bg-danger';
                                        if ($apt['status'] == 'Scheduled') $badge = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $badge ?> px-3 py-2 rounded-pill"><?= $apt['status'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
