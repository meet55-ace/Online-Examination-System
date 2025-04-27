<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'];
    $faculty_name = $_POST['faculty_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE faculty SET faculty_name = ?, email = ? WHERE faculty_id = ?");
    $stmt->bind_param("ssi", $faculty_name, $email, $faculty_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
