# Hospital Management System

This Hospital Management System is a web-based application designed to simplify and organize hospital operations through a secure, role-based platform. The system allows administrators, doctors, and patients to access dedicated dashboards tailored to their responsibilities, helping improve communication, appointment handling, and patient management within a healthcare environment.

The platform includes core features such as appointment scheduling, doctor and patient management, medical history tracking, prescription and note handling, and profile management. Administrators can oversee the entire system, doctors can manage schedules and patient records, and patients can book or cancel appointments based on doctor availability and specialization.

## Features

- **Role-Based Access Control**: Secure login and dashboards for Patients, Doctors, and Administrators.
- **Admin Dashboard**:
  - Manage doctors (register, view)
  - Manage patients
  - View all appointments
  - System-wide notifications
- **Doctor Dashboard**:
  - Manage schedule and availability
  - View upcoming and past appointments
  - Access patient medical history and add notes/prescriptions
  - Profile management
- **Patient Dashboard**:
  - Book appointments with doctors based on specialization and availability
  - View upcoming and past appointments
  - Cancel appointments
  - Profile management

## Tech Stack

- **Backend**: PHP 8+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5, Lucide Icons
- **Architecture**: Procedural PHP with shared includes and helper functions

## Installation

1. **Clone the repository**:
   ```bash
   git clone <repository_url>
   cd hospital-system
   ```

2. **Database Setup**:
   - Create a MySQL database named `hospital_db`.
   - Import the provided SQL dump: `database/hospital-db.sql`.
   - The database comes with pre-seeded users for testing (check the SQL file for details).

3. **Environment Configuration**:
   - Copy the example environment file:
     ```bash
     cp .env.example .env
     ```
   - Update the `.env` file with your database credentials if they differ from the defaults.

4. **Seed the Database**:
   - Run the provided seed script to populate testing accounts:
     ```bash
     php database/seed.php
     ```

5. **Run the Application**:
   - Serve the application using a local server like XAMPP, WAMP, or PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Access the application at `http://localhost:8000`.

## Testing Credentials

You can use the following default accounts to test the system roles:

- **Admin Account**: `admin@hospital.com` | Password: `password123`
- **Doctor Account**: `doctor@hospital.com` | Password: `password123`
- **Patient Account**: `patient@hospital.com` | Password: `password123`

## Directory Structure

- `/admin` - Administrator modules
- `/doctor` - Doctor modules
- `/patient` - Patient modules
- `/auth` - Authentication and authorization
- `/includes` - Shared UI components (header, footer, sidebars) and helper scripts
- `/config` - Database connection and global configuration
- `/assets` - Static assets (CSS, JS, images)
- `/database` - Database schema and seed files



