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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #28a745;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🏥 Hospital System Login</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Form submits to itself -->
        <form method="POST" action="login.php">
            
            <label for="email">Email Address:</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   placeholder="Enter your email"
                   required>
            
            <label for="password">Password:</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   placeholder="Enter your password"
                   required>
            
            <button type="submit" name="login">Login</button>
        </form>
        
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>