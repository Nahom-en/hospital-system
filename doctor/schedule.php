<?php
require_once '../includes/auth_helper.php';
require_role(2);
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get doctor_id
try {
    $stmt = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        $error = "Doctor profile not found. Please contact an administrator.";
        $doctor_id = null;
    } else {
        $doctor_id = $doctor['doctor_id'];
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $doctor_id = null;
}

$days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_schedule']) && $doctor_id) {
    try {
        $pdo->beginTransaction();
        
        foreach ($days_of_week as $day) {
            $is_available = isset($_POST['available'][$day]) ? true : false;
            
            if ($is_available) {
                $start_time = $_POST['start_time'][$day];
                $end_time = $_POST['end_time'][$day];
                
                if (empty($start_time) || empty($end_time)) {
                    throw new Exception("Please provide both start and end times for " . $day);
                }
                
                // Insert or Update (Upsert)
                $stmt = $pdo->prepare("
                    INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time) 
                    VALUES (?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE start_time = ?, end_time = ?
                ");
                $stmt->execute([$doctor_id, $day, $start_time, $end_time, $start_time, $end_time]);
            } else {
                // Delete if exists
                $stmt = $pdo->prepare("DELETE FROM doctor_schedule WHERE doctor_id = ? AND day_of_week = ?");
                $stmt->execute([$doctor_id, $day]);
            }
        }
        
        $pdo->commit();
        $success = "Schedule saved successfully!";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Failed to save schedule: " . $e->getMessage();
    }
}

// Fetch existing schedule
$schedule_map = [];
if ($doctor_id) {
    try {
        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM doctor_schedule WHERE doctor_id = ?");
        $stmt->execute([$doctor_id]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($schedules as $s) {
            $schedule_map[$s['day_of_week']] = [
                'start_time' => substr($s['start_time'], 0, 5), // 'HH:MM:SS' to 'HH:MM'
                'end_time' => substr($s['end_time'], 0, 5)
            ];
        }
    } catch (PDOException $e) {
        $error = "Failed to fetch schedule: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule - Hospital System</title>
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
                    <a href="./schedule.php" class="active">
                        <i data-lucide="clock"></i>
                        <span>Schedule</span>
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
        <form action="schedule.php" method="POST">
            <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5 gap-4">
                <div class="d-flex align-items-center">
                    <button type="button" class="mobile-toggle me-3" id="mobile-toggle">
                        <i data-lucide="menu"></i>
                    </button>
                    <h1 class="header-title h2 mb-0">Manage <span class="text-primary">Schedule</span></h1>
                </div>
                
                <button type="submit" name="save_schedule" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm" <?= !$doctor_id ? 'disabled' : '' ?>>
                    <i data-lucide="save" size="18"></i>
                    Save Changes
                </button>
            </header>

            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i data-lucide="alert-circle" size="20"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i data-lucide="check-circle" size="20"></i>
                            <div><?php echo htmlspecialchars($success); ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm overflow-hidden p-4">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-1">Weekly Availability</h5>
                            <p class="text-muted small">Set your regular working hours to allow patients to book appointments.</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="py-3 text-uppercase small text-muted">Day of Week</th>
                                        <th class="py-3 text-uppercase small text-muted">Start Time</th>
                                        <th class="py-3 text-uppercase small text-muted">End Time</th>
                                        <th class="py-3 text-uppercase small text-muted text-center">Available</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($days_of_week as $day): ?>
                                        <?php 
                                            $is_checked = isset($schedule_map[$day]);
                                            $start_time = $is_checked ? $schedule_map[$day]['start_time'] : '09:00';
                                            $end_time = $is_checked ? $schedule_map[$day]['end_time'] : '17:00';
                                            $row_class = $is_checked ? 'fw-bold' : 'fw-bold text-muted';
                                        ?>
                                        <tr class="border-bottom">
                                            <td class="<?= $row_class ?> py-4"><?= $day ?></td>
                                            <td><input type="time" name="start_time[<?= $day ?>]" class="form-control" value="<?= $start_time ?>" <?= !$is_checked ? 'disabled' : '' ?>></td>
                                            <td><input type="time" name="end_time[<?= $day ?>]" class="form-control" value="<?= $end_time ?>" <?= !$is_checked ? 'disabled' : '' ?>></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input available-toggle" type="checkbox" name="available[<?= $day ?>]" value="1" <?= $is_checked ? 'checked' : '' ?> style="width: 2.5rem; height: 1.25rem;">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

        // Toggle inputs when checkbox is unchecked
        document.querySelectorAll('.available-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const row = this.closest('tr');
                const inputs = row.querySelectorAll('input[type="time"]');
                const label = row.querySelector('td.fw-bold');
                
                inputs.forEach(input => {
                    input.disabled = !this.checked;
                });

                if (this.checked) {
                    label.classList.remove('text-muted');
                } else {
                    label.classList.add('text-muted');
                }
            });
        });
    </script>
</body>
</html>
