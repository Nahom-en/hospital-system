<?php
require_once 'config/database.php';

try {
    // Insert roles if they don't exist
    $pdo->exec("
        INSERT IGNORE INTO roles (role_id, rolename) VALUES 
        (1, 'Patient'),
        (2, 'Doctor'),
        (3, 'Admin');
    ");

    // Create the master admin account
    $email = 'admin@hospital.com';
    $password = 'masterpassword123'; // The 1 master password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role_id) VALUES (?, ?, 3)");
        $stmt->execute([$email, $hashed_password]);
        echo "Master admin account created successfully.<br>";
        echo "Email: admin@hospital.com<br>";
        echo "Password: masterpassword123<br>";
    } else {
        echo "Master admin account already exists.<br>";
    }

    echo "Database seeded with roles and admin account.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
