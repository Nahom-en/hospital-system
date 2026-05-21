<?php
require_once '../includes/auth_helper.php';
require_role(2); // 2 is Doctor
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    mark_notification_read($pdo, $notification_id, $user_id);
    header("Location: notifications.php");
    exit();
}

$notifications = get_notifications($pdo, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Notifications - Hospital System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <!-- Sidebar -->
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
                    <a href="./appointments.php">
                        <i data-lucide="calendar"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="./schedule.php">
                        <i data-lucide="clock"></i>
                        <span>Schedule</span>
                    </a>
                </li>
                <li>
                    <a href="./profile.php">
                        <i data-lucide="user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="./notifications.php" class="active">
                        <i data-lucide="bell"></i>
                        <span>Notifications</span>
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

    <!-- Main Content -->
    <main class="main-content">
        <header class="d-flex align-items-center mb-5">
            <button class="mobile-toggle me-3" id="mobile-toggle" style="display: none;">
                <i data-lucide="menu"></i>
            </button>
            <h1 class="header-title h2 mb-0">Doctor <span class="text-primary">Notifications</span></h1>
        </header>

        <div class="card border-0 shadow-sm p-4">
            <?php if (count($notifications) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="list-group-item px-0 py-3 <?= !$notif['is_read'] ? 'bg-light' : '' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-3">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-circle flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="bell" size="18"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($notif['title']) ?></h6>
                                        <p class="text-muted small mb-1"><?= htmlspecialchars($notif['message']) ?></p>
                                        <span class="text-muted" style="font-size: 10px;"><?= date('F j, Y, g:i a', strtotime($notif['created_at'])) ?></span>
                                    </div>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <form action="" method="POST" class="m-0">
                                        <input type="hidden" name="notification_id" value="<?= $notif['notification_id'] ?>">
                                        <button type="submit" name="mark_read" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mark as Read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i data-lucide="bell-off" size="48" class="mb-3 opacity-50"></i>
                    <p class="mb-0">You have no notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
