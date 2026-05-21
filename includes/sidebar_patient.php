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
                <a href="./bookappointment.php" class="<?= $current_page == 'bookappointment.php' ? 'active' : '' ?>">
                    <i data-lucide="calendar-plus"></i>
                    <span>Book Appointment</span>
                </a>
            </li>
            <li>
                <a href="./myappointments.php" class="<?= $current_page == 'myappointments.php' ? 'active' : '' ?>">
                    <i data-lucide="calendar"></i>
                    <span>My Appointments</span>
                </a>
            </li>
            <li>
                <a href="./notifications.php" class="<?= $current_page == 'notifications.php' ? 'active' : '' ?>">
                    <i data-lucide="bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="./profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">
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
