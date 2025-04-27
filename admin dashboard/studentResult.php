<?php
include '../db.php';
include '../link.php';

$stored = false;

// Fetch Semesters
$semester_sql = "SELECT student_sem_id, sem_name FROM student_sem ORDER BY sem_name";
$semester_result = $conn->query($semester_sql);

// Fetch Courses
$course_sql = "SELECT course_id, course_name FROM course ORDER BY course_name";
$course_result = $conn->query($course_sql);

// Get Selected Values
$selected_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
$selected_course = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$selected_student = isset($_GET['student']) ? (int)$_GET['student'] : 0;

// Fetch Students
$students = [];
if ($selected_semester > 0 && $selected_course > 0) {
    $student_sql = "SELECT student_id, student_name FROM student WHERE student_sem_id = ? AND course_id = ?";
    $stmt = $conn->prepare($student_sql);
    $stmt->bind_param("ii", $selected_semester, $selected_course);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$exam_results = [];
$obtained_marks = 0;
$max_marks = 0;
$percentage = 0;
$absent_count = 0;
$status = '';

if ($selected_student > 0) {
    // Fetch Exam Results for the Student
    $result_sql = "SELECT sub.subject_name, e.exam_id, SUM(q.ques_mark) AS max_marks, 
                      COALESCE(SUM(sa.marks_obtained), 0) AS marks_obtained, 
                      COUNT(q.question_id) AS total_questions,
                      COUNT(sa.ques_id) AS attempted_questions
               FROM subject sub
               JOIN exam e ON sub.subject_id = e.subject_id
               JOIN question q ON e.exam_id = q.exam_id
               LEFT JOIN student_answers sa ON q.question_id = sa.ques_id AND sa.student_id = ?
               WHERE sub.course_id = ? AND e.student_sem_id = ? 
               GROUP BY sub.subject_id, e.exam_id";
    
    $stmt = $conn->prepare($result_sql);
    $stmt->bind_param("iii", $selected_student, $selected_course, $selected_semester);
    $stmt->execute();
    $exam_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

foreach ($exam_results as $row) { 
    $max_marks += intval($row['max_marks']);
    $obtained_marks += intval($row['marks_obtained']);
    if ($row['attempted_questions'] == 0) {
        $absent_count++;
    }
}

$percentage = ($max_marks > 0) ? round(($obtained_marks / $max_marks) * 100, 2) : 0;
$status = ($absent_count >= 2) ? 'Absent' : ($percentage >= 40 ? 'Passed' : 'Failed');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_result']) && $_POST['confirm_result'] === 'yes' && $max_marks > 0) {
    $insert_result_sql = "INSERT INTO student_results (student_id, semester_id, marks_obtained, max_marks, percentage, status, created_at, is_stored) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW(), 1) 
                          ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), 
                                                  max_marks = VALUES(max_marks), 
                                                  percentage = VALUES(percentage), 
                                                  status = VALUES(status), 
                                                  is_stored = 1";
    
    $stmt = $conn->prepare($insert_result_sql);
    $stmt->bind_param("iiidds", $selected_student, $selected_semester, $obtained_marks, $max_marks, $percentage, $status);
    $stmt->execute();

    foreach ($exam_results as $row) {
        $insert_exam_sql = "INSERT INTO student_exam_results (student_id, exam_id, exam_marks_obtained, exam_max_marks) 
                            VALUES (?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE exam_marks_obtained = VALUES(exam_marks_obtained), 
                                                    exam_max_marks = VALUES(exam_max_marks)";
        
        $stmt = $conn->prepare($insert_exam_sql);
        $stmt->bind_param("iiii", $selected_student, $row['exam_id'], $row['marks_obtained'], $row['max_marks']);
        $stmt->execute();
    }
    $stored = true;
}

$existing_result_sql = "SELECT * FROM student_results WHERE student_id = ? AND semester_id = ?";
$stmt = $conn->prepare($existing_result_sql);
$stmt->bind_param("ii", $selected_student, $selected_semester);
$stmt->execute();
$existing_result = $stmt->get_result()->fetch_assoc();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result</title>
    <link rel="stylesheet" href="css/studentResult.css">
</head>

<body>
    <h1 class="university-name">Exam Ease</h1>
    <div class="result-container">
        <h2>Filter Results</h2>
        <form method="GET">
            <label for="semester">Select Semester:</label>
            <select name="semester" id="semester" onchange="this.form.submit()">
                <option value="">-- Select Semester --</option>
                <?php while ($row = $semester_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['student_sem_id']; ?>"
                    <?php echo ($selected_semester == $row['student_sem_id']) ? 'selected' : ''; ?>>
                    <?php echo $row['sem_name']; ?>
                </option>
                <?php } ?>
            </select>

            <label for="course">Select Course:</label>
            <select name="course" id="course" onchange="this.form.submit()">
                <option value="">-- Select Course --</option>
                <?php while ($row = $course_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['course_id']; ?>"
                    <?php echo ($selected_course == $row['course_id']) ? 'selected' : ''; ?>>
                    <?php echo $row['course_name']; ?>
                </option>
                <?php } ?>
            </select>
        </form>

        <?php if (!empty($students)) { ?>
        <form method="GET">
            <input type="hidden" name="semester" value="<?php echo $selected_semester; ?>">
            <input type="hidden" name="course" value="<?php echo $selected_course; ?>">

            <label for="student">Select Student:</label>
            <select name="student" id="student" onchange="this.form.submit()">
                <option value="">-- Select Student --</option>
                <?php foreach ($students as $student) { ?>
                <option value="<?php echo $student['student_id']; ?>"
                    <?php echo ($selected_student == $student['student_id']) ? 'selected' : ''; ?>>
                    <?php echo $student['student_name'] . " (ID: " . $student['student_id'] . ")"; ?>
                </option>
                <?php } ?>
            </select>

        </form>
        <?php } ?>

        <?php if (!empty($exam_results)) { ?>
        <?php if ($absent_count >= 2 && $obtained_marks > 0) { ?>
        <div class="watermark">ABSENT</div>
        <?php } elseif ($obtained_marks > 0) { ?>

        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Max Marks</th>
                    <th>Marks Obtained</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exam_results as $row) { ?>
                <tr>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['max_marks']; ?></td>
                    <td><strong><?php echo $row['marks_obtained']; ?></strong></td>


                </tr>
                <?php } ?>
                <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td><strong><?php echo $max_marks; ?></strong></td>
                    <td><strong><?php echo $obtained_marks; ?></strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"><strong>Percentage</strong></td>
                    <td><strong><?php echo $percentage; ?>%</strong></td>
                </tr>
            </tbody>
        </table>
        <?php } ?>
        <?php } ?>

        <?php if (!$existing_result && !$stored) { ?>
        <form method="POST">
            <p>Do you want to store the result?</p>
            <button type="submit" name="confirm_result" value="yes">Yes</button>
            <button type="submit" name="confirm_result" value="no">No</button>
        </form>
        <?php } ?>

        <div class="fw-bold text-center fs-2 p-5">
            <<<...back to the <a href="dashboard.php">Dashboard</a>
        </div>
    </div>
</body>

</html>