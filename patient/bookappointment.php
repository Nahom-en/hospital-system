<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
</head>
<body>

<div class ="side-bar">
        <ul>
            <li><a href="./dashboard.php">Dashboard</a></li>
            <li><a href="./bookappointment.php">Book Appointment</a></li>
            <li><a href="./myappointments.php">My Appointments</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
        <div class ="content">
            <h1>Book an Appointment</h1>
                <Form:post>
                    <label for="doctor">Doctor:</label>
                    <select name="doctor" id="doctor" required>
                        <option value="">Select a doctor</option>
                        <option value="dr_smith">Dr. Smith</option>
                        <option value="dr_johnson">Dr. Johnson</option>
                        <!-- Add more doctors as needed -->
                    </select>

                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" required>

                    <label for="time">Time:</label>
                    <input type="time" name="time" id="time" required>

                    <label for="Reason">Write Message</label>
                    <textarea name="Reason" id="Reason" required></textarea>

                    <button><a href="./dashboard.php">Cancel</a></button>

                    <button type="submit">Book Appointment</button>
                </Form:post>
        </div>
    </div>
</body>
</html>