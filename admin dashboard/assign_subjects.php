<?php
include '../db.php';

if (isset($_POST['faculty_id']) && isset($_POST['subjects'])) {
    $faculty_id = $_POST['faculty_id'];
    $subjects = $_POST['subjects'];

    foreach ($subjects as $subject_id) {
        $checkQuery = "SELECT * FROM faculty_subject WHERE faculty_id = ? AND subject_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $faculty_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $insertQuery = "INSERT INTO faculty_subject (faculty_id, subject_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ii", $faculty_id, $subject_id);
            $stmt->execute();
        }
    }
    echo "success";
} else {
    echo "error";
}
?>
