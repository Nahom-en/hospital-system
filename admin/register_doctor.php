<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

$error = '';
$success = '';

if (isset($_POST['register_doctor'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $specialization = trim($_POST['specialization']);
    $phone_number = trim($_POST['phone_number']);
    $bio = trim($_POST['bio']);

    if (empty($email) || empty($password) || empty($confirm_password) || empty($specialization)) {
        $error = "Email, Password, Confirm Password, and Specialization are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "Email already registered!";
            } else {
                $pdo->beginTransaction();

                try {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert into users table with role_id = 2 (Doctor)
                    $stmt = $pdo->prepare("INSERT INTO users (email, password, role_id) VALUES (?, ?, 2)");
                    $stmt->execute([$email, $hashed_password]);
                    
                    $user_id = $pdo->lastInsertId();

                    // Insert into doctors table
                    $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialization, phone_number, bio) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $specialization, $phone_number, $bio]);

                    $pdo->commit();
                    $success = "Doctor registered successfully!";
                    
                    // Clear form variables
                    $email = $specialization = $phone_number = $bio = '';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to register doctor: " . $e->getMessage();
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Doctor - Admin Dashboard</title>
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
                    <a href="./dashboard.php">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./manage_doctors.php">
                        <i data-lucide="stethoscope"></i>
                        <span>Manage Doctors</span>
                    </a>
                </li>
                <li>
                    <a href="./manage_patients.php">
                        <i data-lucide="users"></i>
                        <span>Manage Patients</span>
                    </a>
                </li>
                <li>
                    <a href="./all_appointments.php">
                        <i data-lucide="calendar"></i>
                        <span>All Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="./register_doctor.php" class="active">
                        <i data-lucide="user-plus"></i>
                        <span>Register Doctor</span>
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
                <h1 class="header-title h2 mb-0 fw-bold">Register <span class="text-primary">Doctor</span></h1>
            </div>
        </header>

        <div class="card border-0 shadow-sm rounded-4 max-w-2xl">
            <div class="card-body p-5">
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

                <form method="POST" action="">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Account Information</h5>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address *</label>
                            <input type="email" class="form-control bg-light border-0 py-2" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password *</label>
                            <input type="password" class="form-control bg-light border-0 py-2" id="password" name="password" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label small fw-bold text-muted text-uppercase">Confirm Password *</label>
                            <input type="password" class="form-control bg-light border-0 py-2" id="confirm_password" name="confirm_password" minlength="6" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 border-bottom pb-3 mt-5">Professional Details</h5>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="specialization" class="form-label small fw-bold text-muted text-uppercase">Specialization *</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="specialization" name="specialization" placeholder="e.g. Cardiologist" value="<?php echo isset($specialization) ? htmlspecialchars($specialization) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="phone_number" name="phone_number" value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>">
                        </div>
                        <div class="col-md-12">
                            <label for="bio" class="form-label small fw-bold text-muted text-uppercase">Professional Bio</label>
                            <textarea class="form-control bg-light border-0 py-2" id="bio" name="bio" rows="4"><?php echo isset($bio) ? htmlspecialchars($bio) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" name="register_doctor" class="btn btn-primary px-5 py-2 fw-bold">Register Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
