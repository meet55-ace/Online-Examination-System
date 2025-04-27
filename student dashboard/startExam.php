<?php
include '../db.php';
include 'student_session.php';

date_default_timezone_set('Asia/Kolkata');

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Validate exam_id
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : null;
if (!$exam_id) {
    die("Exam ID is missing or invalid.");
}

$student_id = $_SESSION['student_id'] ?? null;
if (!$student_id) {
    die("Invalid request: Student not logged in.");
}

// Fetch exam details
$exam_query = "SELECT exam_title, exam_start_time, exam_end_time FROM exam WHERE exam_id = ?";
$stmt_exam = $conn->prepare($exam_query);
$stmt_exam->bind_param("i", $exam_id);
$stmt_exam->execute();
$result_exam = $stmt_exam->get_result();

if ($result_exam->num_rows === 0) {
    die("Exam not found.");
}

$exam = $result_exam->fetch_assoc();
$exam_title = $exam['exam_title'];
$exam_start_time = strtotime($exam['exam_start_time']);
$exam_end_time = strtotime($exam['exam_end_time']);
$current_time = time();

// Check if the student has already started or completed the exam
$check_status_query = "SELECT status FROM exam_attempt WHERE student_id = ? AND exam_id = ?";
$stmt_check = $conn->prepare($check_status_query);
$stmt_check->bind_param("ii", $student_id, $exam_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $attempt = $result_check->fetch_assoc();
    if ($attempt['status'] == 'Completed') {
        die("You have already completed this exam.");
    }
}

// Insert a new record for the student starting the exam with status 'Present'
$insert_attempt_query = "INSERT INTO exam_attempt (exam_id, student_id, status, attempt_at) 
                         VALUES (?, ?, 'Present', NOW()) 
                         ON DUPLICATE KEY UPDATE status = 'Present'";
$stmt_insert = $conn->prepare($insert_attempt_query);
$stmt_insert->bind_param("ii", $exam_id, $student_id);
$stmt_insert->execute();

// Calculate remaining time for the exam
$remaining_seconds = max(0, $exam_end_time - $current_time);

// If exam time is over, prevent access
if ($current_time >= $exam_end_time) {
    die("Exam has already ended.");
}


$total_marks_query = "SELECT SUM(ques_mark) AS total_marks FROM question WHERE exam_id = ?";
$stmt_total_marks = $conn->prepare($total_marks_query);
if (!$stmt_total_marks) {
    die("Error preparing total marks query: " . $conn->error);
}
$stmt_total_marks->bind_param("i", $exam_id);
$stmt_total_marks->execute();
$result_total_marks = $stmt_total_marks->get_result();

$total_marks = $result_total_marks->fetch_assoc()['total_marks'] ?? 0;



// Fetch previously selected answers
$student_answers = [];
$answer_query = "SELECT ques_id, selected_option FROM student_answers WHERE exam_id = ? AND student_id = ?";
$stmt_answers = $conn->prepare($answer_query);
$stmt_answers->bind_param("ii", $exam_id, $student_id);
$stmt_answers->execute();
$result_answers = $stmt_answers->get_result();

while ($row = $result_answers->fetch_assoc()) {
    $student_answers[$row['ques_id']] = $row['selected_option'];
}

// Fetch questions for the exam
$question_query = "SELECT question_id, question, op1, op2, op3, op4, ques_mark FROM question WHERE exam_id = ? ORDER BY RAND()";
$stmt_question = $conn->prepare($question_query);
$stmt_question->bind_param("i", $exam_id);
$stmt_question->execute();
$result_question = $stmt_question->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($exam_title); ?> - Questions</title>
    <link rel="stylesheet" href="./css/startExam.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <h1><?= htmlspecialchars($exam_title); ?></h1>
            <p><strong>Start Time:</strong> <?= date("h:i A", $exam_start_time); ?> &nbsp;&nbsp;
                <strong>End Time:</strong> <?= date("h:i A", $exam_end_time); ?>
            </p>
            <p><strong>Total Marks:</strong> <?= $total_marks; ?></p>

            <div class="time_circles" id="examTimer" data-timer="<?= $remaining_seconds; ?>">00:00</div>
        </div>

        <form id="examForm">
    <?php while ($question = $result_question->fetch_assoc()) { ?>
        <div class="question-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3><?= htmlspecialchars($question['question']); ?></h3>
                <span class="question-marks"><?= htmlspecialchars($question['ques_mark']); ?> Marks</span>
            </div>
            <div class="options">
                <?php foreach (['A' => 'op1', 'B' => 'op2', 'C' => 'op3', 'D' => 'op4'] as $key => $option) {
                    $checked = isset($student_answers[$question['question_id']]) && $student_answers[$question['question_id']] == $key ? 'checked' : '';
                ?>
                    <label>
                        <input type="radio" name="answer_<?= $question['question_id']; ?>" value="<?= $key; ?>" <?= $checked; ?> required>
                        <?= htmlspecialchars($key); ?>. <?= htmlspecialchars($question[$option]); ?>
                    </label>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <button type="submit" class="submit-btn" id="submitBtn">Submit Exam</button>
</form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const examTimer = document.getElementById('examTimer');
            const submitBtn = document.getElementById('submitBtn');
            const examForm = document.getElementById('examForm');
            let remainingSeconds = parseInt(examTimer.getAttribute('data-timer'), 10);

            // Disable the submit button initially
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.5";

            function updateTimer() {
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    submitExam();
                    return;
                }
                remainingSeconds--;
                const minutes = Math.floor(remainingSeconds / 60);
                const seconds = remainingSeconds % 60;
                examTimer.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (remainingSeconds <= 60) {
                    examTimer.style.color = "#dc3545";
                }
            }

            const timerInterval = setInterval(updateTimer, 1000);

            function submitExam() {
                clearInterval(timerInterval);
                submitBtn.disabled = true;

                const formData = new FormData(examForm);
                formData.append('exam_id', <?= json_encode($exam_id); ?>);

                fetch('submit_exam.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Exam submitted successfully.");
                            window.location.href = data.redirect;
                        } else {
                            alert("Error submitting exam: " + data.message);
                        }
                    })
                    .catch(error => alert("Error occurred while submitting the exam."));
            }
        });
    </script>

</body>

</html>
