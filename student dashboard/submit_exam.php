<?php
include '../db.php';
include 'student_session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$student_id = $_SESSION['student_id'] ?? null;
$exam_id = $_POST['exam_id'] ?? null;

if (!$student_id || !$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$total_marks = 0;

// Loop through submitted answers
foreach ($_POST as $key => $selected_option) {
    if (strpos($key, 'answer_') === 0) {
        $ques_id = str_replace('answer_', '', $key);
        $ques_id = intval($ques_id);

        // Fetch the correct answer and marks from the question table
        $query = "SELECT correct_option, ques_mark FROM question WHERE question_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ques_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $question = $result->fetch_assoc();
            $correct_answer = $question['correct_option']; 
            $marks = $question['ques_mark']; // Fetch the actual question marks

            // Determine if the answer is correct
            $status = ($selected_option === $correct_answer) ? 'right' : 'wrong';
            $obtained_marks = ($status === 'right') ? $marks : 0;
            $total_marks += $obtained_marks;

            // Insert into student_answers table
            $insert_query = "INSERT INTO student_answers (exam_id, student_id, ques_id, selected_option, correct_option, student_answer_status, marks_obtained, created_at) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW()) 
                             ON DUPLICATE KEY UPDATE selected_option = VALUES(selected_option), student_answer_status = VALUES(student_answer_status), marks_obtained = VALUES(marks_obtained)";
            $stmt_insert = $conn->prepare($insert_query);
            $stmt_insert->bind_param("iiisssi", $exam_id, $student_id, $ques_id, $selected_option, $correct_answer, $status, $obtained_marks);
            $stmt_insert->execute();
        }
    }
}

// Update exam_attempt status
// $update_attempt = "UPDATE exam_attempt SET status = 'Completed' WHERE student_id = ? AND exam_id = ?";
// $stmt_update = $conn->prepare($update_attempt);
// $stmt_update->bind_param("ii", $student_id, $exam_id);
// $stmt_update->execute();

echo json_encode(['success' => true, 'message' => 'Exam submitted successfully.', 'redirect' => 'examList.php']);
exit;
?>
