<?php
include '../db.php';
include 'student_session.php';

$loggedInStudentId = $_SESSION['student_id'];

$updateExamStatusQuery = "UPDATE exam SET exam_status = 'Completed' WHERE exam_end_time < NOW() AND exam_status != 'Completed'";
$conn->query($updateExamStatusQuery);

// Fetch the student's semester ID
$studentQuery = "SELECT student_sem_id FROM student WHERE student_id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $loggedInStudentId);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows > 0) {
    $student = $studentResult->fetch_assoc();
    $studentSemesterId = $student['student_sem_id'];

    // Mark "Absent" for past exams where the student didn't attempt
    $updateAbsentQuery = "
    INSERT INTO exam_attempt (exam_id, student_id, status)
    SELECT e.exam_id, s.student_id, 'Absent'
    FROM exam e
    JOIN student s ON e.student_sem_id = s.student_sem_id
    LEFT JOIN exam_attempt ea 
        ON e.exam_id = ea.exam_id AND s.student_id = ea.student_id
    WHERE e.student_sem_id = ? 
    AND e.exam_end_time < NOW()
    AND ea.exam_id IS NULL";

    $updateAbsentStmt = $conn->prepare($updateAbsentQuery);
    $updateAbsentStmt->bind_param("i", $studentSemesterId);
    $updateAbsentStmt->execute();

    // Fetch exams for the student's semester with attempt status, but exclude completed exams
    $examQuery = "
    SELECT exam.*, 
    COALESCE(
        (SELECT MAX(status) FROM exam_attempt 
         WHERE exam_attempt.exam_id = exam.exam_id 
         AND exam_attempt.student_id = ?), 
        'Not Attempted'
    ) AS attempt_status 
    FROM exam 
    WHERE student_sem_id = ? 
    AND exam.exam_end_time > NOW()";  // Exclude completed exams

    $examStmt = $conn->prepare($examQuery);
    $examStmt->bind_param("ii", $loggedInStudentId, $studentSemesterId);
    $examStmt->execute();
    $exams = $examStmt->get_result();
} else {
    $exams = null;
}

$serverCurrentTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Exam</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./css/examList.css">
</head>

<body>
    <h1>Start Exam</h1>
    <div class="exam-container">
        <?php if ($exams && mysqli_num_rows($exams) > 0) { ?>
            <?php while ($exam = mysqli_fetch_assoc($exams)) {
                $examStartTime = new DateTime($exam['exam_start_time'], new DateTimeZone('Asia/Kolkata'));
                $examEndTime = new DateTime($exam['exam_end_time'], new DateTimeZone('Asia/Kolkata'));
                $currentTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

                $attemptStatus = $exam['attempt_status'];
                $isOngoing = $currentTime >= $examStartTime && $currentTime <= $examEndTime;
                $isUpcoming = $currentTime < $examStartTime;
                $isClosed = $currentTime > $examEndTime;

                // Determine Exam Status
                $examStatus = $isOngoing ? 'Ongoing' : ($isUpcoming ? 'Pending' : 'Completed');
            ?>
                <div class="exam-card">
                    <h3><?= htmlspecialchars($exam['exam_title']); ?></h3>
                    <p><strong>Start Time:</strong> <?= $examStartTime->format('d-m-Y h:i A'); ?></p>
                    <p><strong>End Time:</strong> <?= $examEndTime->format('d-m-Y h:i A'); ?></p>
                    <p><strong>Status:</strong> <?= $examStatus; ?></p>

                    <button class="start-btn"
                        onclick="startExam('<?= $exam['exam_id']; ?>', '<?= htmlspecialchars($exam['exam_title']); ?>')"
                        <?php if ($attemptStatus === 'Completed' || $attemptStatus === 'Absent' || $isClosed || !$isOngoing) {
                            echo 'disabled';
                        } ?>>

                        <?= $attemptStatus === 'Completed' ? 'Attempted' : ($attemptStatus === 'Absent' ? 'Absent' : ($isClosed ? 'Exam Closed' : ($isOngoing ? 'Start' : 'Not Available'))); ?>
                    </button>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No exams available for your semester.</p>
        <?php } ?>
    </div>

    <script>
        function startExam(examId, examTitle) {
            Swal.fire({
                title: `Start Exam: ${examTitle}?`,
                text: "Once started, you cannot leave until the exam ends!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Start",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `startExam.php?exam_id=${examId}`;
                }
            });
        }
    </script>
    
    <div class="back-link">
        <a href="studentDashboard.php"><<< Back to Dashboard</a>
    </div>
</body>

</html>
