<?php
require_once '../includes/auth_helper.php';
require_role(2); // Role 2 = Doctor
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = trim($_POST['specialization']);
    $phone_number = trim($_POST['phone_number']);
    $bio = trim($_POST['bio']);

    if (empty($specialization)) {
        $error = "Specialization is required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE doctors SET specialization = ?, phone_number = ?, bio = ? WHERE user_id = ?");
            $stmt->execute([$specialization, $phone_number, $bio, $user_id]);
            $success = "Profile updated successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch current doctor data
try {
    $stmt = $pdo->prepare("
        SELECT u.email, d.specialization, d.phone_number, d.bio 
        FROM users u 
        JOIN doctors d ON u.user_id = d.user_id 
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        $error = "Doctor profile not found.";
        $doctor = ['email' => '', 'specialization' => '', 'phone_number' => '', 'bio' => ''];
    }
} catch (PDOException $e) {
    $error = "Error fetching profile: " . $e->getMessage();
    $doctor = ['email' => '', 'specialization' => '', 'phone_number' => '', 'bio' => ''];
}
?>
<?php
$page_title = 'My Profile - Doctor Dashboard';
require_once '../includes/header.php';
require_once '../includes/sidebar_doctor.php';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-5">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3" id="mobile-toggle">
                    <i data-lucide="menu"></i>
                </button>
                <h1 class="header-title h2 mb-0">My <span class="text-primary">Profile</span></h1>
            </div>
            <a href="./dashboard.php" class="btn btn-light px-4 py-2 fw-bold d-none d-md-flex align-items-center gap-2 border shadow-sm">
                <i data-lucide="arrow-left" size="18"></i>
                Back to Dashboard
            </a>
        </header>

        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="d-flex align-items-center gap-4 mb-5 pb-3 border-bottom">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i data-lucide="stethoscope" size="40"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">Professional Details</h2>
                            <p class="text-muted small mb-0">Update your public medical profile</p>
                        </div>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-3 mb-4">
                            <i data-lucide="alert-circle" size="20"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-3 mb-4">
                            <i data-lucide="check-circle" size="20"></i>
                            <div><?= htmlspecialchars($success) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Account Email</label>
                                <input type="email" id="email" class="form-control px-3 bg-light" value="<?= htmlspecialchars($doctor['email'] ?? '') ?>" readonly>
                                <small class="text-muted mt-1 d-block">Contact administration to change your email.</small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="specialization" class="form-label small fw-bold text-muted text-uppercase">Specialization</label>
                                <input type="text" id="specialization" name="specialization" class="form-control px-3" value="<?= htmlspecialchars($doctor['specialization'] ?? '') ?>" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone_number" class="form-label small fw-bold text-muted text-uppercase">Contact Phone</label>
                                <input type="tel" id="phone_number" name="phone_number" class="form-control px-3" value="<?= htmlspecialchars($doctor['phone_number'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label for="bio" class="form-label small fw-bold text-muted text-uppercase">Professional Bio</label>
                                <textarea id="bio" name="bio" class="form-control px-3" rows="5" placeholder="Briefly describe your experience and medical philosophy..."><?= htmlspecialchars($doctor['bio'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold">
                                    <i data-lucide="save" class="me-2"></i>
                                    Save Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
