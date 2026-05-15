<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hospital System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

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
                    <a href="#">
                        <i data-lucide="users"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                <li>
                    <a href="./register_doctor.php">
                        <i data-lucide="user-plus"></i>
                        <span>Register Doctor</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i data-lucide="settings"></i>
                        <span>System Settings</span>
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
            <div>
                <p class="text-muted small fw-bold text-uppercase mb-1">Administrator Control Panel</p>
                <h1 class="header-title h2 mb-0 fw-bold">System <span class="text-primary">Overview</span></h1>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-12 col-md-4">
                <div class="card p-4 shadow-sm border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i data-lucide="users"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Total Users</p>
                            <h3 class="fw-bold mb-0">1,284</h3>
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
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Active Sessions</p>
                            <h3 class="fw-bold mb-0">42</h3>
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
                            <h3 class="fw-bold mb-0 text-success">Healthy</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info border-0 rounded-4 p-4 shadow-sm">
            <h5 class="fw-bold mb-2">Welcome to the Admin Panel</h5>
            <p class="mb-0">You have full access to manage hospital operations, staff accounts, and patient records.</p>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
