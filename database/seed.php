<?php
require_once __DIR__ . '/../config/database.php';

try {
    $password = password_hash('password123', PASSWORD_DEFAULT);

    // Insert Roles
    $pdo->exec("INSERT IGNORE INTO roles (role_id, rolename) VALUES (1, 'Patient'), (2, 'Doctor'), (3, 'Admin')");

    // Insert Admin
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (user_id, email, password, role_id) VALUES (1, 'admin@hospital.com', ?, 3)");
    $stmt->execute([$password]);

    // Insert Doctor
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (user_id, email, password, role_id) VALUES (2, 'doctor@hospital.com', ?, 2)");
    $stmt->execute([$password]);
    $pdo->exec("INSERT IGNORE INTO doctors (doctor_id, user_id, firstname, lastname, specialization, phone_number, bio) VALUES (1, 2, 'John', 'Doe', 'Cardiology', '1234567890', 'Expert cardiologist')");

    // Insert Patient
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (user_id, email, password, role_id) VALUES (3, 'patient@hospital.com', ?, 1)");
    $stmt->execute([$password]);
    $pdo->exec("INSERT IGNORE INTO patient (patient_id, user_id, firstname, lastname, phone_number, gender, dob, address) VALUES (1, 3, 'Jane', 'Smith', '0987654321', 'Female', '1990-01-01', '123 Health Ave')");

    echo "Database seeded successfully with testing credentials!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
