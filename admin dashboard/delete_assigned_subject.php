<?php
include '../db.php';

$faculty_subject_id = $_POST['faculty_subject_id'];

$deleteQuery = "DELETE FROM faculty_subject WHERE faculty_subject_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $faculty_subject_id);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
