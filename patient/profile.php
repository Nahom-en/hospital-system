<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hospital System</title>
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
                    <a href="./profile.php" class="active">
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
        <header class="d-flex align-items-center mb-5">
            <button class="mobile-toggle" id="mobile-toggle">
                <i data-lucide="menu"></i>
            </button>
            <h1 class="header-title h2 mb-0">My <span class="text-primary">Profile</span></h1>
        </header>

        <div class="row">
            <div class="col-12">
                <!-- Personal Information -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="d-flex align-items-center gap-4 mb-5 pb-3 border-bottom">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i data-lucide="user" size="40"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">Nahom</h2>
                            <p class="text-muted small mb-0">Manage your personal details</p>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary"></i>
                        Personal Information
                    </h5>
                    <form action="#" method="POST">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="first_name" class="form-label small fw-bold text-muted text-uppercase">First Name</label>
                                <input type="text" id="first_name" class="form-control px-3" value="Nahom" placeholder="Enter first name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_name" class="form-label small fw-bold text-muted text-uppercase">Last Name</label>
                                <input type="text" id="last_name" class="form-control px-3" value="Doe" placeholder="Enter last name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" id="email" class="form-control px-3 bg-light" value="nahom@example.com" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                                <input type="tel" id="phone" class="form-control px-3" value="+251 911 234 567" placeholder="Enter phone number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label small fw-bold text-muted text-uppercase">Gender</label>
                                <select id="gender" class="form-select px-3">
                                    <option value="male" selected>Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="dob" class="form-label small fw-bold text-muted text-uppercase">Date of Birth (DOB)</label>
                                <input type="date" id="dob" class="form-control px-3" value="1995-01-01">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label small fw-bold text-muted text-uppercase">Address</label>
                                <textarea id="address" class="form-control px-3" rows="3" placeholder="Enter your full address">Bole, Addis Ababa, Ethiopia</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold">
                                    <i data-lucide="save" class="me-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
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
    </script>
</body>
</html>

