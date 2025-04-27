<?php
include '../db.php';
include 'faculty_session.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$student_id = $_POST['student_id'] ?? null;
$faculty_id = $_POST['faculty_id'] ?? null;

if (!$student_id || !$faculty_id) {
    echo json_encode(["error" => "Missing student_id or faculty_id"]);
    exit;
}

// Debugging: Log student & faculty ID
file_put_contents("debug.log", "Fetching Marks: Student ID = $student_id, Faculty ID = $faculty_id\n", FILE_APPEND);

$query = "SELECT q.subject_id, sub.subject_name, q.question, q.op1, q.op2, q.op3, q.op4, q.correct_option, 
       sa.selected_option, sa.student_answer_status, sa.marks_obtained, q.ques_mark
       FROM student_answers sa
       JOIN question q ON sa.ques_id = q.question_id
       JOIN subject sub ON q.subject_id = sub.subject_id
       WHERE sa.student_id = ? 
       AND q.subject_id IN (SELECT subject_id FROM faculty_subject WHERE faculty_id = ?)";


$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die(json_encode(["error" => "SQL Prepare Failed: " . mysqli_error($conn)]));
}

mysqli_stmt_bind_param($stmt, "ii", $student_id, $faculty_id);

if (!mysqli_stmt_execute($stmt)) {
    die(json_encode(["error" => "SQL Execute Failed: " . mysqli_stmt_error($stmt)]));
}

$result = mysqli_stmt_get_result($stmt);

$marks_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $subject_name = $row['subject_name'];

    if (!isset($marks_data[$subject_name])) {
        $marks_data[$subject_name] = [];
    }

    $marks_data[$subject_name][] = [
        'question' => $row['question'],
        'options' => [
            'A' => $row['op1'],
            'B' => $row['op2'],
            'C' => $row['op3'],
            'D' => $row['op4']
        ],
        'selected_option' => $row['selected_option'],
        'correct_option' => $row['correct_option'],
        'student_answer_status' => $row['student_answer_status'],
        'marks_obtained' => $row['marks_obtained'],
        'ques_mark' => $row['ques_mark']
    ];
}

// Debugging: Log output
file_put_contents("debug.log", "Response: " . json_encode($marks_data) . "\n", FILE_APPEND);

echo json_encode($marks_data);
?>
