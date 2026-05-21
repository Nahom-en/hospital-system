<?php
require_once '../includes/auth_helper.php';
require_role(1);
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone_number = trim($_POST['phone_number']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['dob']);
    $address = trim($_POST['address']);

    if (empty($firstname) || empty($lastname) || empty($gender) || empty($dob)) {
        $error = "First Name, Last Name, Gender, and Date of Birth are required.";
    } else {
        try {
            // Check if patient record exists
            $stmt = $pdo->prepare("SELECT patient_id FROM patient WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                // Update
                $stmt = $pdo->prepare("UPDATE patient SET firstname = ?, lastname = ?, phone_number = ?, gender = ?, dob = ?, address = ? WHERE user_id = ?");
                $stmt->execute([$firstname, $lastname, $phone_number, $gender, $dob, $address, $user_id]);
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO patient (user_id, firstname, lastname, phone_number, gender, dob, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $firstname, $lastname, $phone_number, $gender, $dob, $address]);
            }
            $success = "Profile updated successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch current user data
try {
    $stmt = $pdo->prepare("
        SELECT u.email, p.firstname, p.lastname, p.phone_number, p.gender, p.dob, p.address 
        FROM users u 
        LEFT JOIN patient p ON u.user_id = p.user_id 
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $user = ['email' => '', 'firstname' => '', 'lastname' => '', 'phone_number' => '', 'gender' => '', 'dob' => '', 'address' => ''];
    }
} catch (PDOException $e) {
    $error = "Error fetching profile: " . $e->getMessage();
    $user = ['email' => '', 'firstname' => '', 'lastname' => '', 'phone_number' => '', 'gender' => '', 'dob' => '', 'address' => ''];
}
?>
<?php
$page_title = 'My Profile - Hospital System';
require_once '../includes/header.php';
require_once '../includes/sidebar_patient.php';
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
            <div class="col-12">
                <!-- Personal Information -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="d-flex align-items-center gap-4 mb-5 pb-3 border-bottom">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i data-lucide="user" size="40"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0"><?= htmlspecialchars(!empty($user['firstname']) ? $user['firstname'] . ' ' . $user['lastname'] : 'Patient') ?></h2>
                            <p class="text-muted small mb-0">Manage your personal details</p>
                        </div>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i data-lucide="alert-circle" size="20"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i data-lucide="check-circle" size="20"></i>
                            <div><?php echo htmlspecialchars($success); ?></div>
                        </div>
                    <?php endif; ?>

                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary"></i>
                        Personal Information
                    </h5>
                    <form action="profile.php" method="POST">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="firstname" class="form-label small fw-bold text-muted text-uppercase">First Name</label>
                                <input type="text" id="firstname" name="firstname" class="form-control px-3" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="lastname" class="form-label small fw-bold text-muted text-uppercase">Last Name</label>
                                <input type="text" id="lastname" name="lastname" class="form-control px-3" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" id="email" class="form-control px-3 bg-light" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone_number" class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                                <input type="tel" id="phone_number" name="phone_number" class="form-control px-3" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label small fw-bold text-muted text-uppercase">Gender</label>
                                <select id="gender" name="gender" class="form-select px-3" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male"   <?= (isset($user['gender']) && $user['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (isset($user['gender']) && $user['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other"  <?= (isset($user['gender']) && $user['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="dob" class="form-label small fw-bold text-muted text-uppercase">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control px-3" value="<?= htmlspecialchars($user['dob'] ?? '') ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label small fw-bold text-muted text-uppercase">Address</label>
                                <textarea id="address" name="address" class="form-control px-3" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold">
                                    <i data-lucide="save" class="me-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
