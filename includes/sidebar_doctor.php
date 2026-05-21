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
                <a href="./appointments.php" class="<?= $current_page == 'appointments.php' ? 'active' : '' ?>">
                    <i data-lucide="calendar"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li>
                <a href="./schedule.php" class="<?= $current_page == 'schedule.php' ? 'active' : '' ?>">
                    <i data-lucide="clock"></i>
                    <span>Schedule</span>
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
