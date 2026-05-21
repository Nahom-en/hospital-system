<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
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
<?php
$page_title = 'Notifications - Admin Dashboard';
require_once '../includes/header.php';
require_once '../includes/sidebar_admin.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="d-flex align-items-center mb-5">
            <button class="mobile-toggle me-3" id="mobile-toggle" style="display: none;">
                <i data-lucide="menu"></i>
            </button>
            <h1 class="header-title h2 mb-0">System <span class="text-primary">Notifications</span></h1>
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
                    <p class="mb-0">No system notifications.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
