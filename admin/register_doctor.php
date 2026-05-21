<?php
require_once '../includes/auth_helper.php';
require_role(3); // 3 is Admin
require_once '../config/database.php';

$error = '';
$success = '';

if (isset($_POST['register_doctor'])) {
    $firstname    = trim($_POST['firstname']);
    $lastname     = trim($_POST['lastname']);
    $email        = trim($_POST['email']);
    $password     = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $specialization   = trim($_POST['specialization']);
    $phone_number     = trim($_POST['phone_number']);
    $bio          = trim($_POST['bio']);

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password) || empty($specialization)) {
        $error = "First Name, Last Name, Email, Password, and Specialization are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "Email already registered!";
            } else {
                $pdo->beginTransaction();
                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("INSERT INTO users (email, password, role_id) VALUES (?, ?, 2)");
                    $stmt->execute([$email, $hashed_password]);
                    $user_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("INSERT INTO doctors (user_id, firstname, lastname, specialization, phone_number, bio) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $firstname, $lastname, $specialization, $phone_number, $bio]);

                    $pdo->commit();
                    $success = "Doctor Dr. {$firstname} {$lastname} registered successfully!";

                    $firstname = $lastname = $email = $specialization = $phone_number = $bio = '';
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
<?php
$page_title = 'Register Doctor - Admin Dashboard';
require_once '../includes/header.php';
require_once '../includes/sidebar_admin.php';
?>

    <main class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Administrator Control Panel</p>
                    <h1 class="header-title h2 mb-0 fw-bold">Register <span class="text-primary">Doctor</span></h1>
                </div>
            </div>
        </header>

        <div class="card border-0 shadow-sm rounded-4" style="max-width: 800px;">
            <div class="card-body p-5">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                        <i data-lucide="alert-circle" size="20"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                        <i data-lucide="check-circle" size="20"></i>
                        <div><?= htmlspecialchars($success) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Personal Information</h5>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label small fw-bold text-muted text-uppercase">First Name *</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="firstname" name="firstname" 
                                   placeholder="e.g. John"
                                   value="<?= isset($firstname) ? htmlspecialchars($firstname) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label small fw-bold text-muted text-uppercase">Last Name *</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="lastname" name="lastname"
                                   placeholder="e.g. Smith"
                                   value="<?= isset($lastname) ? htmlspecialchars($lastname) : '' ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 border-bottom pb-3 mt-5">Account Information</h5>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address *</label>
                            <input type="email" class="form-control bg-light border-0 py-2" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
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
                            <input type="text" class="form-control bg-light border-0 py-2" id="specialization" name="specialization" placeholder="e.g. Cardiologist" value="<?= isset($specialization) ? htmlspecialchars($specialization) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="phone_number" name="phone_number" value="<?= isset($phone_number) ? htmlspecialchars($phone_number) : '' ?>">
                        </div>
                        <div class="col-md-12">
                            <label for="bio" class="form-label small fw-bold text-muted text-uppercase">Professional Bio</label>
                            <textarea class="form-control bg-light border-0 py-2" id="bio" name="bio" rows="4"><?= isset($bio) ? htmlspecialchars($bio) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" name="register_doctor" class="btn btn-primary px-5 py-2 fw-bold">
                            <i data-lucide="user-plus" size="18" class="me-2"></i>Register Doctor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
