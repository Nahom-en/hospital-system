<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

// Handle deletion
$message = '';
if (isset($_POST['delete_doctor_id'])) {
    $doctor_id = $_POST['delete_doctor_id'];
    try {
        // Find the user_id associated with this doctor to delete from users table
        // Due to CASCADE, deleting from users will delete the doctor record
        $stmt = $pdo->prepare("SELECT user_id FROM doctors WHERE doctor_id = ?");
        $stmt->execute([$doctor_id]);
        $uid = $stmt->fetchColumn();
        
        if ($uid) {
            $delStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $delStmt->execute([$uid]);
            $message = "<div class='alert alert-success border-0 shadow-sm'>Doctor account deleted successfully.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger border-0 shadow-sm'>Error deleting doctor: " . $e->getMessage() . "</div>";
    }
}

// Fetch all doctors
try {
    $stmt = $pdo->query("
        SELECT d.doctor_id, d.specialization, d.phone_number, u.email, u.created_at
        FROM doctors d
        JOIN users u ON d.user_id = u.user_id
        ORDER BY d.doctor_id DESC
    ");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Error fetching doctors.</div>";
    $doctors = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Admin Dashboard</title>
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
                    <a href="./manage_doctors.php" class="active">
                        <i data-lucide="stethoscope"></i>
                        <span>Manage Doctors</span>
                    </a>
                </li>
                <li>
                    <a href="./manage_patients.php">
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
                    <h1 class="header-title h2 mb-0 fw-bold">Manage <span class="text-primary">Doctors</span></h1>
                </div>
            </div>
            <a href="./register_doctor.php" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-2 border shadow-sm">
                <i data-lucide="plus" size="18"></i>
                Add Doctor
            </a>
        </header>

        <?= $message ?>

        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase small fw-bold text-muted py-3">ID</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Email</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Specialization</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Phone</th>
                            <th class="text-uppercase small fw-bold text-muted py-3">Joined</th>
                            <th class="text-uppercase small fw-bold text-muted py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($doctors) > 0): ?>
                            <?php foreach ($doctors as $doc): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $doc['doctor_id'] ?></td>
                                    <td><?= htmlspecialchars($doc['email']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($doc['specialization']) ?></span></td>
                                    <td><?= htmlspecialchars($doc['phone_number'] ?? 'N/A') ?></td>
                                    <td class="text-muted small"><?= date('M j, Y', strtotime($doc['created_at'])) ?></td>
                                    <td class="text-end">
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to completely delete this doctor account?');">
                                            <input type="hidden" name="delete_doctor_id" value="<?= $doc['doctor_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger px-3 rounded-pill fw-bold">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No doctors found in the system.</td>
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
