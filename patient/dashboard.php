<?php
require_once '../includes/auth_helper.php';
require_role(1); // 1 is Patient
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

$user_id = $_SESSION['user_id'];
$notifications = get_notifications($pdo, $user_id, 3);
// Fetch patient details like first name
$stmt = $pdo->prepare("SELECT firstname FROM patient WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient = $stmt->fetch();
$first_name = $patient ? $patient['firstname'] : 'Patient';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Hospital System</title>
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
                    <a href="./dashboard.php" class="active">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./bookappointment.php">
                        <i data-lucide="calendar-plus"></i>
                        <span>Book Appointment</span>
                    </a>
                </li>
                <li>
                    <a href="./myappointments.php">
                        <i data-lucide="calendar"></i>
                        <span>My Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="./profile.php">
                        <i data-lucide="user"></i>
                        <span>Profile</span>
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
                    <p class="h2 fw-bold mb-0">2</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-success">
                        <i data-lucide="check-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Completed Appointments</h3>
                    <p class="h2 fw-bold mb-0">12</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-warning">
                        <i data-lucide="alert-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Pending Appointments</h3>
                    <p class="h2 fw-bold mb-0">1</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 h-100">
                    <div class="stat-icon icon-danger">
                        <i data-lucide="x-circle"></i>
                    </div>
                    <h3 class="text-muted small fw-bold text-uppercase mb-1">Cancelled Appointments</h3>
                    <p class="h2 fw-bold mb-0">0</p>
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
                    <div class="card-header bg-primary py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-white fw-bold mb-0">Upcoming Visit</h5>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold">Confirmed</span>
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
                                        <p class="h6 fw-bold mb-0">Dr. Sarah Johnson</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                                        <i data-lucide="clock-3" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small fw-semibold text-uppercase mb-1">Date & Time</p>
                                        <p class="h6 fw-bold mb-0">May 20, 2024 - 10:30 AM</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-4 mt-2">
                                    <p class="text-muted small fw-semibold text-uppercase mb-2"><i data-lucide="sticky-note" size="14" class="me-1"></i> Meeting Notes</p>
                                    <p class="mb-0 small text-dark">Routine checkup and blood work discussion. Please bring your latest test results if available.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button class="btn btn-primary px-4">View Details</button>
                            <button class="btn btn-outline-secondary px-4">Reschedule</button>
                        </div>
                    </div>
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


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Toggle Logic
        const mobileToggle = document.getElementById('mobile-toggle');
        const sidebar = document.getElementById('sidebar');

        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && 
                !sidebar.contains(e.target) && 
                !mobileToggle.contains(e.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Dynamic Greeting
        const greetingEl = document.getElementById('time-greeting');
        const hour = new Date().getHours();
        if (hour < 12) greetingEl.innerText = "Good Morning";
        else if (hour < 17) greetingEl.innerText = "Good Afternoon";
        else greetingEl.innerText = "Good Evening";
    </script>
</body>
</html>

