<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['patient_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $diagnosis = trim($_POST['diagnosis']);
    $doctor_id = !empty($_POST['doctor_id']) ? $_POST['doctor_id'] : NULL;

    if (empty($name) || empty($email) || empty($phone) || empty($age) || empty($gender) || empty($diagnosis)) {
        $_SESSION['status'] = '<div class="alert alert-danger">Error: All fields (except Doctor) are mandatory.</div>';
        header("Location: list.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = '<div class="alert alert-danger">Error: Invalid email format.</div>';
        header("Location: list.php");
        exit();
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $_SESSION['status'] = '<div class="alert alert-danger">Error: Phone number must be exactly 10 digits.</div>';
        header("Location: list.php");
        exit();
    }

    $sql = "INSERT INTO patient (patient_name, email, phone, age, gender, diagnosis, doctor_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssissi", $name, $email, $phone, $age, $gender, $diagnosis, $doctor_id);

        try {
            if ($stmt->execute()) {
                $_SESSION['status'] = '<div class="alert alert-success">Patient <strong>' . htmlspecialchars($name) . '</strong> added successfully!</div>';
            }
        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $_SESSION['status'] = '<div class="alert alert-warning">Error: The email <strong>' . htmlspecialchars($email) . '</strong> is already registered.</div>';
            } else {
                $_SESSION['status'] = '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
            }
        }

        $stmt->close();
    } else {
        $_SESSION['status'] = '<div class="alert alert-danger">Connection Error: ' . $conn->error . '</div>';
    }
}

header("Location: list.php");
exit();
?>