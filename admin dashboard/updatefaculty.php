<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faculty_id = $_POST['faculty_id'];
    $faculty_name = $_POST['faculty_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("UPDATE faculty SET faculty_name = ?, email = ?, phone_number = ? WHERE faculty_id = ?");
    $stmt->bind_param("sssi", $faculty_name, $email, $phone_number, $faculty_id);

    if ($stmt->execute()) {
        header("Location: manage_faculty.php?status=updated");
    } else {
        header("Location: manage_faculty.php?status=error");
    }
}
?>
