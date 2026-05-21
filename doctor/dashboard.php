<?php
require_once '../includes/auth_helper.php';
require_role(2); // 2 is Doctor
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Hospital System</title>
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
                    <a href="./notifications.php">
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
        <!-- Welcome Section -->
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" id="time-greeting">Welcome</p>
                    <h1 class="header-title h2 mb-0 fw-bold">Welcome, <span class="text-primary">Dr. Smith</span></h1>
                </div>
            </div>
        </header>

        <!-- Stat Cards Section -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i data-lucide="calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Today's Appointments</p>
                            <h3 class="fw-bold mb-0">8</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                            <i data-lucide="calendar-clock"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Pending Requests</p>
                            <h3 class="fw-bold mb-0">5</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success-subtle text-success p-3 rounded-circle">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Completed Today</p>
                            <h3 class="fw-bold mb-0">3</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info-subtle text-info p-3 rounded-circle">
                            <i data-lucide="users"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-bold text-uppercase">Total Patients</p>
                            <h3 class="fw-bold mb-0">142</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Today Schedule Table -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0">Today's Schedule</h5>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Patient Name</th>
                                        <th class="py-3">Time</th>
                                        <th class="py-3">Reason</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3 text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">John Doe</td>
                                        <td>09:00 AM</td>
                                        <td><span class="text-muted small">Routine Checkup</span></td>
                                        <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span></td>
                                        <td class="text-end pe-4">
                                            <a href="patient_details.php?id=1" class="btn btn-sm btn-light border" title="View Patient"><i data-lucide="eye" size="16"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">Jane Smith</td>
                                        <td>10:30 AM</td>
                                        <td><span class="text-muted small">Blood Pressure</span></td>
                                        <td><span class="badge bg-success px-3 py-2 rounded-pill">Approved</span></td>
                                        <td class="text-end pe-4">
                                            <a href="patient_details.php?id=2" class="btn btn-sm btn-light border" title="View Patient"><i data-lucide="eye" size="16"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">Robert Johnson</td>
                                        <td>01:00 PM</td>
                                        <td><span class="text-muted small">Flu Symptoms</span></td>
                                        <td><span class="badge bg-primary px-3 py-2 rounded-pill">Completed</span></td>
                                        <td class="text-end pe-4">
                                            <a href="patient_details.php?id=3" class="btn btn-sm btn-light border" title="View Patient"><i data-lucide="eye" size="16"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Summary (Sidebar) -->
            <div class="col-12 col-xl-4">
                <div class="d-flex flex-column gap-4">
                    <!-- Recent Patients -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                            <h5 class="fw-bold mb-0">Recent Patients</h5>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 border-bottom d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i data-lucide="user" size="20" class="text-muted"></i>
                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0">Emma Wilson</p>
                                            <p class="text-muted small mb-0">Asthma Review</p>
                                        </div>
                                    </div>
                                    <a href="patient_details.php?id=4" class="btn btn-sm btn-link text-primary p-0"><i data-lucide="chevron-right"></i></a>
                                </li>
                                <li class="list-group-item px-0 border-bottom d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i data-lucide="user" size="20" class="text-muted"></i>
                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0">Michael Brown</p>
                                            <p class="text-muted small mb-0">Skin Allergy</p>
                                        </div>
                                    </div>
                                    <a href="patient_details.php?id=5" class="btn btn-sm btn-link text-primary p-0"><i data-lucide="chevron-right"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Upcoming Follow-ups -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                            <h5 class="fw-bold mb-0">Upcoming Follow-ups</h5>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div class="alert alert-light border d-flex align-items-center gap-3 mb-2">
                                <i data-lucide="calendar" class="text-primary"></i>
                                <div>
                                    <p class="fw-bold mb-0 small">David Lee</p>
                                    <p class="text-muted small mb-0">Tomorrow, 10:00 AM</p>
                                </div>
                            </div>
                            <div class="alert alert-light border d-flex align-items-center gap-3 mb-0">
                                <i data-lucide="calendar" class="text-primary"></i>
                                <div>
                                    <p class="fw-bold mb-0 small">Sarah Davis</p>
                                    <p class="text-muted small mb-0">May 16, 02:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Dynamic Greeting
        const greetingEl = document.getElementById('time-greeting');
        if (greetingEl) {
            const hour = new Date().getHours();
            if (hour < 12) greetingEl.innerText = "Good Morning";
            else if (hour < 17) greetingEl.innerText = "Good Afternoon";
            else greetingEl.innerText = "Good Evening";
        }
    </script>
</body>
</html>
