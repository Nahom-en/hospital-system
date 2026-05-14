<?php
session_start();
require_once '../config/database.php';

// Initialize messages
$error = '';
$success = '';

// Check if form submitted
if (isset($_POST['register'])) {

    // Get and sanitize input
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    else {

        try {

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {

                $error = "Email already registered!";

            } else {

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Default role_id = 1 (patient)
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, role_id)
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([$email, $hashed_password, 1]);

                $success = "Registration successful! You can now login.";

                // Optional redirect
                // header("Location: login.php");
                // exit();
            }

        } catch (PDOException $e) {

            // Don't expose database errors in production
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hospital System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Custom Auth CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Left Side: Info (Desktop Only) -->
            <div class="auth-info-side">
                <div class="mb-auto">
                    <!-- Logo removed -->
                </div>
                <div class="mb-auto py-5">
                    <h1 class="display-5 fw-bold mb-4">Join Our <br><span class="text-primary">Community.</span></h1>
                    <p class="lead opacity-75">Experience the future of healthcare management. Secure, simple, and patient-centered.</p>
                </div>
                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-3 p-3 bg-white bg-opacity-10 rounded-4">
                        <div class="bg-primary p-2 rounded-circle">
                            <i data-lucide="user-plus" class="text-dark" size="20"></i>
                        </div>
                        <p class="small mb-0">Join thousands of patients managing their health smarter.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="auth-form-side">
                <div class="mb-5">
                    <h2 class="fw-bold mb-2">Create Account</h2>
                    <p class="text-muted">Start your journey with us today.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
                        <i data-lucide="alert-circle" size="20"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success border-0 rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
                        <i data-lucide="check-circle" size="20"></i>
                        <div><?php echo htmlspecialchars($success); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                                <i data-lucide="mail" size="18" class="text-muted"></i>
                            </span>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control border-2 border-start-0 rounded-end-3" 
                                   placeholder="name@example.com"
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                                <i data-lucide="lock" size="18" class="text-muted"></i>
                            </span>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control border-2 border-start-0 rounded-end-3" 
                                   placeholder="Min. 6 characters"
                                   minlength="6"
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label small fw-bold text-muted text-uppercase">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                                <i data-lucide="shield-check" size="18" class="text-muted"></i>
                            </span>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-control border-2 border-start-0 rounded-end-3" 
                                   placeholder="Repeat password"
                                   minlength="6"
                                   required>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 py-3 mb-4">
                        Create Account
                    </button>

                    <div class="text-center">
                        <p class="text-muted small">Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>