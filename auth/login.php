<?php
// STEP 1: Start session - THIS IS CRUCIAL!
// Session lets us remember the user is logged in across pages
session_start();

// STEP 2: Include database connection
require_once '../config/database.php';

// STEP 3: Initialize error variable
$error = '';

// STEP 4: Check if user is already logged in
// If yes, redirect them to dashboard (no need to login again)
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

// STEP 5: Check if form was submitted
if (isset($_POST['login'])) {
    
    // STEP 6: Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // STEP 7: Validate inputs are not empty
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password!";
    } else {
        
        // STEP 8: Try to find user in database
        try {
            // Query to get user by email
            // We select user_id, email, password, and role_id
            $stmt = $pdo->prepare("SELECT user_id, email, password, role_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            // STEP 9: Check if user exists
            if ($stmt->rowCount() === 1) {
                
                // STEP 10: Get user data
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // STEP 11: Verify password
                // password_verify() compares plain password with hashed password
                if (password_verify($password, $user['password'])) {
                    
                    // ✅ PASSWORD CORRECT! Login successful
                    
                    // STEP 12: Store user info in session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['logged_in'] = true;
                    
                    // STEP 13: Redirect to dashboard
                    header("Location: ../dashboard.php");
                    exit();
                    
                } else {
                    // ❌ Password incorrect
                    $error = "Invalid email or password!";
                }
                
            } else {
                // ❌ User not found
                // Note: We use same message as wrong password for security
                // (Don't reveal if email exists or not)
                $error = "Invalid email or password!";
            }
            
        } catch(PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hospital System</title>
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
                    <h1 class="display-5 fw-bold mb-4">Your Health, <br><span class="text-primary">Simplified.</span></h1>
                    <p class="lead opacity-75">Connect with top physicians, manage your appointments, and track your health journey in one place.</p>
                </div>
                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-3 p-3 bg-white bg-opacity-10 rounded-4">
                        <div class="bg-primary p-2 rounded-circle">
                            <i data-lucide="shield-check" class="text-dark" size="20"></i>
                        </div>
                        <p class="small mb-0">Secure and HIPAA-compliant health data management.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="auth-form-side">
                <div class="mb-5">
                    <h2 class="fw-bold mb-2">Welcome Back</h2>
                    <p class="text-muted">Please enter your credentials to access your account.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
                        <i data-lucide="alert-circle" size="20"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
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
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="form-label small fw-bold text-muted text-uppercase mb-0">Password</label>
                            <a href="#" class="small text-primary fw-bold text-decoration-none">Forgot Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                                <i data-lucide="lock" size="18" class="text-muted"></i>
                            </span>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control border-2 border-start-0 rounded-end-3" 
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 py-3 mb-4">
                        Login to Account
                    </button>

                    <div class="text-center">
                        <p class="text-muted small">Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Create Account</a></p>
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