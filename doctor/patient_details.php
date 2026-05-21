<?php
require_once '../includes/auth_helper.php';
require_role(2);
?>
<?php
$page_title = 'Patient Details - Hospital System';
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
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="./appointments.php" class="text-decoration-none text-muted">Appointments</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Patient Details</li>
                        </ol>
                    </nav>
                    <h1 class="header-title h3 mb-0 fw-bold">Jane Smith</h1>
                </div>
            </div>
            <a href="./appointments.php" class="btn btn-light px-4 py-2 fw-bold d-none d-md-flex align-items-center gap-2 border shadow-sm">
                <i data-lucide="arrow-left" size="18"></i>
                Back
            </a>
        </header>

        <div class="row g-4">
            <!-- Left Column: Patient Info -->
            <div class="col-12 col-xl-4">
                <!-- Patient Information -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <span class="fs-1 fw-bold">JS</span>
                        </div>
                        <h4 class="fw-bold mb-1">Jane Smith</h4>
                        <p class="text-muted small mb-0">ID: #PAT-84729</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Female, 28 y/o</span>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Blood: A+</span>
                        </div>
                    </div>
                    
                    <hr class="opacity-10 my-4">
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i data-lucide="phone" class="text-muted mt-1" size="18"></i>
                            <div>
                                <p class="small text-muted text-uppercase fw-bold mb-0">Phone</p>
                                <p class="mb-0">+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i data-lucide="mail" class="text-muted mt-1" size="18"></i>
                            <div>
                                <p class="small text-muted text-uppercase fw-bold mb-0">Email</p>
                                <p class="mb-0">jane.smith@example.com</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i data-lucide="map-pin" class="text-muted mt-1" size="18"></i>
                            <div>
                                <p class="small text-muted text-uppercase fw-bold mb-0">Address</p>
                                <p class="mb-0">123 Health Ave, Medical City</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i data-lucide="calendar-clock" class="text-primary"></i>
                        Upcoming Appointments
                    </h5>
                    <div class="alert alert-primary border-0 p-3 mb-0 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-primary">May 25, 2024</span>
                            <span class="badge bg-white text-primary rounded-pill">10:30 AM</span>
                        </div>
                        <p class="small mb-0">Follow-up for Blood Pressure</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Medical Data -->
            <div class="col-12 col-xl-8">
                <!-- Tabs -->
                <ul class="nav nav-pills mb-4 gap-2" id="patientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 fw-bold rounded-pill" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab" aria-selected="true">Appointment History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 fw-bold rounded-pill" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes" type="button" role="tab" aria-selected="false">Medical Notes</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 fw-bold rounded-pill" id="prescriptions-tab" data-bs-toggle="pill" data-bs-target="#prescriptions" type="button" role="tab" aria-selected="false">Prescriptions</button>
                    </li>
                </ul>

                <div class="tab-content" id="patientTabsContent">
                    <!-- Appointment History Tab -->
                    <div class="tab-pane fade show active" id="history" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">Date</th>
                                            <th class="py-3">Reason</th>
                                            <th class="py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-4 fw-bold">May 15, 2024</td>
                                            <td>Blood Pressure Check</td>
                                            <td><span class="badge bg-primary px-3 py-2 rounded-pill">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-bold">March 10, 2024</td>
                                            <td>General Consultation</td>
                                            <td><span class="badge bg-primary px-3 py-2 rounded-pill">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-bold">January 05, 2024</td>
                                            <td>Annual Physical</td>
                                            <td><span class="badge bg-primary px-3 py-2 rounded-pill">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Notes Tab -->
                    <div class="tab-pane fade" id="notes" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-light">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Add New Note</label>
                                    <textarea class="form-control border-2" rows="3" placeholder="Enter patient observations, symptoms, or diagnoses..."></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary px-4 fw-bold">Save Note</button>
                                </div>
                            </form>
                        </div>

                        <div class="card border-0 shadow-sm p-4">
                            <div class="border-bottom pb-4 mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="fw-bold mb-0">Blood Pressure Elevated</h6>
                                    <span class="text-muted small">May 15, 2024</span>
                                </div>
                                <p class="text-muted small mb-0">Patient exhibits slightly elevated blood pressure (135/85). Recommended lifestyle changes and scheduled a follow-up in two weeks.</p>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="fw-bold mb-0">Annual Physical Clear</h6>
                                    <span class="text-muted small">January 05, 2024</span>
                                </div>
                                <p class="text-muted small mb-0">All vitals are within normal ranges. No immediate concerns.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Prescriptions Tab -->
                    <div class="tab-pane fade" id="prescriptions" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-light">
                            <form class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Add Prescription</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control border-2" placeholder="Medication Name">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control border-2" placeholder="Dosage (e.g., 50mg)">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control border-2" rows="2" placeholder="Instructions (e.g., Take twice daily after meals)"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-primary px-4 fw-bold">Add Prescription</button>
                                </div>
                            </form>
                        </div>

                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">Medication</th>
                                            <th class="py-3">Dosage</th>
                                            <th class="py-3">Instructions</th>
                                            <th class="py-3">Date Prescribed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">Lisinopril</td>
                                            <td>10mg</td>
                                            <td class="small text-muted">Take one tablet daily in the morning.</td>
                                            <td>May 15, 2024</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">Ibuprofen</td>
                                            <td>400mg</td>
                                            <td class="small text-muted">Take as needed for pain.</td>
                                            <td>March 10, 2024</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
