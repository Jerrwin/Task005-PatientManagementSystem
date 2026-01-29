<?php
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary">Patient Management System</h1>
        <p class="lead text-muted">Select a module to manage records.</p>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-5 mb-4">
            <div class="card shadow-lg border-0 h-100 text-center hover-card">
                <div class="card-body p-5">
                    <div class="mb-3">
                        <img src="assets/images/patients.png" alt="Patients Icon" width="64" height="64">
                    </div>
                    <h3 class="card-title">Patients</h3>
                    <p class="card-text text-muted">Add, view, and manage patient records and diagnoses.</p>
                    <a href="patientPage.php" class="btn btn-outline-primary btn-lg w-100 stretched-link">
                        Manage Patients
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card shadow-lg border-0 h-100 text-center hover-card">
                <div class="card-body p-5">
                    <div class="mb-3">
                        <img src="assets/images/doctors.png" alt="Doctors Icon" width="64" height="64">
                    </div>
                    <h3 class="card-title">Doctors</h3>
                    <p class="card-text text-muted">View doctor profiles and their specializations.</p>
                    <a href="doctorPage.php" class="btn btn-outline-danger btn-lg w-100 stretched-link">
                        Manage Doctors
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>