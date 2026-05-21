USE hospital_db;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS notification;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctor_schedule;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS patient;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- Roles table (should be created first due to foreign key)
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    rolename VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Increased for hashed passwords
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Patient table
CREATE TABLE patient (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    dob DATE NOT NULL,
    address VARCHAR(200),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Doctors table
CREATE TABLE doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    bio TEXT,  -- Changed to TEXT for longer bios
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Doctor schedule table
CREATE TABLE doctor_schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_day (doctor_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointments table
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    reason VARCHAR(200),
    status ENUM('Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No-show') DEFAULT 'Scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE,
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notification table
CREATE TABLE notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_unread (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO roles (role_id, rolename) VALUES 
(1, 'Patient'),
(2, 'Doctor'),
(3, 'Admin');

-- Insert default testing users (password: password123)
INSERT INTO users (user_id, email, password, role_id) VALUES 
(1, 'admin@hospital.com', '$2y$12$jHLMps/Ve7Ei/fXMZvfd8eo5UWPVTGy31gUP77toUeL9lzpp6Fj.G', 3),
(2, 'doctor@hospital.com', '$2y$12$jHLMps/Ve7Ei/fXMZvfd8eo5UWPVTGy31gUP77toUeL9lzpp6Fj.G', 2),
(3, 'patient@hospital.com', '$2y$12$jHLMps/Ve7Ei/fXMZvfd8eo5UWPVTGy31gUP77toUeL9lzpp6Fj.G', 1);

-- Insert default testing doctor profile
INSERT INTO doctors (doctor_id, user_id, firstname, lastname, specialization, phone_number, bio) VALUES 
(1, 2, 'John', 'Doe', 'Cardiology', '1234567890', 'Expert cardiologist');

-- Insert default testing patient profile
INSERT INTO patient (patient_id, user_id, firstname, lastname, phone_number, gender, dob, address) VALUES 
(1, 3, 'Jane', 'Smith', '0987654321', 'Female', '1990-01-01', '123 Health Ave');