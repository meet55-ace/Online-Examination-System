<?php
include '../db.php';

if (isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'];

    $deleteSubjects = $conn->prepare("DELETE FROM faculty_subject WHERE faculty_id = ?");
    $deleteSubjects->bind_param("i", $faculty_id);
    $deleteSubjects->execute();

    $deleteFaculty = $conn->prepare("DELETE FROM faculty WHERE faculty_id = ?");
    $deleteFaculty->bind_param("i", $faculty_id);
    $deleteFaculty->execute();

    echo "success";
}
?>
