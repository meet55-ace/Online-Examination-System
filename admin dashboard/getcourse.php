<?php
include '../db.php';

if (isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    $query = $conn->prepare("SELECT c.course_name FROM course c JOIN subject s ON c.course_id = s.course_id WHERE s.subject_id = ?");
    $query->bind_param("i", $subject_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo $row['course_name'];
    } else {
        echo "Course not found";
    }
}
?>
