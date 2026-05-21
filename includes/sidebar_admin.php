<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="./dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="./manage_doctors.php" class="<?= $current_page == 'manage_doctors.php' ? 'active' : '' ?>">
                    <i data-lucide="stethoscope"></i>
                    <span>Manage Doctors</span>
                </a>
            </li>
            <li>
                <a href="./manage_patients.php" class="<?= $current_page == 'manage_patients.php' ? 'active' : '' ?>">
                    <i data-lucide="users"></i>
                    <span>Manage Patients</span>
                </a>
            </li>
            <li>
                <a href="./all_appointments.php" class="<?= $current_page == 'all_appointments.php' ? 'active' : '' ?>">
                    <i data-lucide="calendar"></i>
                    <span>All Appointments</span>
                </a>
            </li>
            <li>
                <a href="./register_doctor.php" class="<?= $current_page == 'register_doctor.php' ? 'active' : '' ?>">
                    <i data-lucide="user-plus"></i>
                    <span>Register Doctor</span>
                </a>
            </li>
            <li>
                <a href="./notifications.php" class="<?= $current_page == 'notifications.php' ? 'active' : '' ?>">
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
