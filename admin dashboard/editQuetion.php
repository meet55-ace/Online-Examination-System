<?php
include '../db.php';

$exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;
$exam_id = $_GET['exam_id'] ?? null;


if (!$exam_id) {
    // echo "<p>Error: Exam ID is not specified. Please provide a valid exam ID.</p>";
    exit();  
}

$query = "SELECT * FROM question WHERE exam_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exam_id);  // Bind the exam_id as an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $questions = [];
    while ($question = $result->fetch_assoc()) {
        $questions[] = $question;
    }
} else {
    echo "<p>No questions found for this exam.</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_question'])) {
    $question_id = $_POST['question_id'];
    $updated_question = $_POST['updated_question'];
    $updated_op1 = $_POST['updated_op1'];
    $updated_op2 = $_POST['updated_op2'];
    $updated_op3 = $_POST['updated_op3'];
    $updated_op4 = $_POST['updated_op4'];
    $updated_correct_option = $_POST['updated_correct_option'];
    $updated_ques_mark = $_POST['updated_ques_mark'];

    $stmt = $conn->prepare("UPDATE question SET question = ?, op1 = ?, op2 = ?, op3 = ?, op4 = ?, correct_option = ?, ques_mark = ? WHERE question_id = ?");
    $stmt->bind_param("ssssssii", $updated_question, $updated_op1, $updated_op2, $updated_op3, $updated_op4, $updated_correct_option, $updated_ques_mark, $question_id);

    if ($stmt->execute()) {
        echo "<script>alert('Question updated successfully!');</script>";
        echo "<script>window.location.href = 'editQuetion.php?exam_id=$exam_id';</script>"; // Redirect to the same page to refresh the list of questions
    } else {
        echo "<script>alert('Error updating question.');</script>";
    }
}

// delete


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id_to_delete'] ?? null;

    if (!$question_id) {
        echo json_encode(['error' => 'Question ID is missing.']);
        exit;
    }

    $sql = "DELETE FROM question WHERE question_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $question_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => 'Question deleted successfully.']);
        } else {
            echo json_encode(['error' => 'No question found with the provided ID.']);
        }
    } else {
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
    }
    exit;
}
