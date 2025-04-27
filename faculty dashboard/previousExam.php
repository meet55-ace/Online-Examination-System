<?php
include '../link.php';
include '../db.php';
include 'faculty_session.php';

$faculty_id = intval($_SESSION['faculty_id']); 

$query = "SELECT faculty_name FROM faculty WHERE faculty_id = $faculty_id";
$result = mysqli_query($conn, $query);
$faculty = mysqli_fetch_assoc($result);
$faculty_name = $faculty['faculty_name'];

$exam_query = "SELECT exam.exam_id, exam.exam_title, exam.exam_start_time, exam.exam_end_time, 
                      subject.subject_name, c.course_name, s.sem_name
               FROM exam 
               JOIN subject ON exam.subject_id = subject.subject_id
               JOIN course c ON subject.course_id = c.course_id
               JOIN student_sem s ON subject.student_sem_id = s.student_sem_id
               WHERE subject.subject_id IN 
               (SELECT subject_id FROM faculty_subject WHERE faculty_id = $faculty_id)
               AND exam.exam_end_time < NOW()";  

$exam_result = mysqli_query($conn, $exam_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Exams</title>
    <link rel="stylesheet" href="./css/previousExam.css">
</head>

<body>

    <div class="main-content">
        <div class="dashboard-content">
            <h2>Previous Exams</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam Title</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($exam_result) > 0) {
                        while ($exam = mysqli_fetch_assoc($exam_result)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($exam['exam_title']); ?></td>
                                <td><?= htmlspecialchars($exam['course_name']); ?></td>
                                <td><?= htmlspecialchars($exam['sem_name']); ?></td>
                                <td><?= date("d-m-Y h:i A", strtotime($exam['exam_start_time'])); ?></td>
                                <td><?= date("d-m-Y h:i A", strtotime($exam['exam_end_time'])); ?></td>
                                <td>
                                    <a href="view_exam.php?exam_id=<?= $exam['exam_id']; ?>" class="btn btn-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No previous exams found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
    
    
    <div class="fw-bold text-center fs-2 p-2">
    <<<...back to the <a href="faculty_dashboard.php">Dashboard</a>
</div>

</body>
</html>
