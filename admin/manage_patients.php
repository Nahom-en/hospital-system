<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

// Handle deletion
$message = '';
if (isset($_POST['delete_patient_id'])) {
    $patient_id = $_POST['delete_patient_id'];
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM patient WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        $uid = $stmt->fetchColumn();
        
        if ($uid) {
            $delStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $delStmt->execute([$uid]);
            $message = "<div class='alert alert-success border-0 shadow-sm'>Patient account deleted successfully.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger border-0 shadow-sm'>Error deleting patient: " . $e->getMessage() . "</div>";
    }
}

// Fetch all patients
try {
    $stmt = $pdo->query("
        SELECT p.patient_id, p.firstname, p.lastname, p.gender, p.phone_number, p.dob, u.email, u.created_at
        FROM patient p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.patient_id DESC
    ");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Error fetching patients.</div>";
    $patients = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="./dashboard.php">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./manage_doctors.php">
                        <i data-lucide="stethoscope"></i>
                        <span>Manage Doctors</span>
                    </a>
                </li>
                <li>
                    <a href="./manage_patients.php" class="active">
                        <i data-lucide="users"></i>
                        <span>Manage Patients</span>
                    </a>
                </li>
                <li>
                    <a href="./all_appointments.php">
                        <i data-lucide="calendar"></i>
                        <span>All Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="./register_doctor.php">
                        <i data-lucide="user-plus"></i>
                        <span>Register Doctor</span>
                    </a>
                </li>
                <li style="margin-top: auto; padding-top: 2rem;">
                    <a href="../auth/logout.php" class="text-danger">
                        <i data-lucide="log-out"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Administrator Control Panel</p>
                    <h1 class="header-title h2 mb-0 fw-bold">Manage <span class="text-primary">Patients</span></h1>
                </div>
            </div>
        </header>

        <?= $message ?>

        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase small fw-bold text-muted py-3">ID</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Name</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Email</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Gender / DOB</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Phone</th>
                            <th class="text-uppercase small fw-bold text-muted py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($patients) > 0): ?>
                            <?php foreach ($patients as $pat): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $pat['patient_id'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($pat['firstname'] . ' ' . $pat['lastname']) ?></td>
                                    <td><?= htmlspecialchars($pat['email']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($pat['gender']) ?> <br>
                                        <small class="text-muted"><?= htmlspecialchars($pat['dob']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($pat['phone_number'] ?? 'N/A') ?></td>
                                    <td class="text-end">
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to completely delete this patient?');">
                                            <input type="hidden" name="delete_patient_id" value="<?= $pat['patient_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger px-3 rounded-pill fw-bold">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No completed patient profiles found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();
        const mobileToggle = document.getElementById('mobile-toggle');
        const sidebar = document.getElementById('sidebar');
        mobileToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
    </script>
</body>
</html>
