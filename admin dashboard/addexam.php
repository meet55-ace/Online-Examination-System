<?php
include '../db.php';

date_default_timezone_set('Asia/Kolkata');

// Fetch subjects based on course_id & student_sem_id
if (isset($_GET['course_id'], $_GET['student_sem_id'])) {
    $course_id = intval($_GET['course_id']);
    $student_sem_id = intval($_GET['student_sem_id']);

    $stmt = $conn->prepare("
        SELECT subject_id, subject_name 
        FROM subject 
        WHERE course_id = ? AND student_sem_id = ?
    ");
    $stmt->bind_param("ii", $course_id, $student_sem_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($subjects);
    exit;
}

// Handle exam submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset(
            $_POST['course_id'],
            $_POST['student_sem_id'],
            $_POST['subject_id'],
            $_POST['exam_title'],
            $_POST['question_limit'],
            $_POST['exam_start_time'],
            $_POST['exam_end_time'],
            // $_POST['exam_marks'],
            $_POST['exam_description']
        )
    ) {
        $course_id = $_POST['course_id'];
        $student_sem_id = $_POST['student_sem_id'];
        $subject_id = $_POST['subject_id'];
        $exam_title = $_POST['exam_title'];
        $question_limit = $_POST['question_limit'];
        $exam_start_time = date('Y-m-d H:i:s', strtotime($_POST['exam_start_time']));
        $exam_end_time = date('Y-m-d H:i:s', strtotime($_POST['exam_end_time']));
        // $exam_marks = $_POST['exam_marks'];
        $exam_description = $_POST['exam_description'];

        // Check if exam already exists
        $stmt_check = $conn->prepare("SELECT * FROM exam WHERE exam_title = ? AND course_id = ? AND student_sem_id = ?");
        $stmt_check->bind_param("sii", $exam_title, $course_id, $student_sem_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<script>alert('Exam already exists!'); window.location.href = 'dashboard.php';</script>";
        } else {
            // Insert new exam
            $stmt_insert = $conn->prepare("
                INSERT INTO `exam` (course_id, student_sem_id, subject_id, exam_title, question_limit, exam_start_time, exam_end_time,  exam_description) 
                VALUES (?, ?, ?,  ?, ?, ?, ?, ?)
            ");
            $stmt_insert->bind_param("iiisisss", $course_id, $student_sem_id, $subject_id, $exam_title, $question_limit, $exam_start_time, $exam_end_time,  $exam_description);

            if ($stmt_insert->execute()) {
                echo "<script>alert('Exam added successfully!'); window.location.href = 'dashboard.php';</script>";
            } else {
                echo "<script>alert('Database error: {$conn->error}'); window.location.href = 'dashboard.php';</script>";
            }
        }
    }
}
$conn->close();
?>
