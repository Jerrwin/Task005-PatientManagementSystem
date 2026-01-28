<?php
session_start();
require_once '../config/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM patient WHERE patient_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['status'] = '<div class="alert alert-warning">Patient deleted successfully.</div>';
    } else {
        $_SESSION['status'] = '<div class="alert alert-danger">Error: Could not delete patient. ' . $conn->error . '</div>';
    }

    $stmt->close();
}

header("Location: list.php");
exit();
?>