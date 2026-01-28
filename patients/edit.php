<?php
require_once '../includes/header.php';
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM patient WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo '<div class="container mt-5 alert alert-danger">Patient not found!</div>';
    exit();
}

$doctors_result = $conn->query("SELECT doctor_id, doctor_name, specialization FROM doctor");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['patient_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $diagnosis = $_POST['diagnosis'];
    $doctor_id = !empty($_POST['doctor_id']) ? $_POST['doctor_id'] : NULL;

    $update_sql = "UPDATE patient SET 
                   patient_name=?, email=?, phone=?, age=?, gender=?, diagnosis=?, doctor_id=? 
                   WHERE patient_id=?";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssissii", $name, $email, $phone, $age, $gender, $diagnosis, $doctor_id, $id);

    if ($update_stmt->execute()) {
        $_SESSION['status'] = '<div class="alert alert-success">Patient updated successfully!</div>';
        header("Location: list.php");
        exit();
    } else {
        $error_message = "Error updating record: " . $conn->error;
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Patient</h4>
                    <a href="list.php" class="btn btn-sm btn-light">Back to List</a>
                </div>
                <div class="card-body">

                    <?php if (isset($error_message))
                        echo "<div class='alert alert-danger'>$error_message</div>"; ?>

                    <form action="edit.php?id=<?php echo $id; ?>" method="POST">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" name="patient_name" class="form-control"
                                    value="<?php echo htmlspecialchars($patient['patient_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($patient['email']); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($patient['phone']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control"
                                    value="<?php echo htmlspecialchars($patient['age']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?php if ($patient['gender'] == 'Male')
                                        echo 'selected'; ?>>Male
                                    </option>
                                    <option value="Female" <?php if ($patient['gender'] == 'Female')
                                        echo 'selected'; ?>>
                                        Female</option>
                                    <option value="Other" <?php if ($patient['gender'] == 'Other')
                                        echo 'selected'; ?>>
                                        Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign Doctor</label>
                            <select name="doctor_id" class="form-select">
                                <option value="">-- Unassigned --</option>
                                <?php
                                if ($doctors_result->num_rows > 0) {
                                    while ($doc = $doctors_result->fetch_assoc()) {
                                        $selected = ($patient['doctor_id'] == $doc['doctor_id']) ? 'selected' : '';

                                        echo '<option value="' . $doc['doctor_id'] . '" ' . $selected . '>' . "Dr. " .
                                            htmlspecialchars($doc['doctor_name']) . ' (' . htmlspecialchars($doc['specialization']) . ')' .
                                            '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control"
                                rows="3"><?php echo htmlspecialchars($patient['diagnosis']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Patient</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>