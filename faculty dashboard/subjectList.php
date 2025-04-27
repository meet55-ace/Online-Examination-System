<?php
include '../link.php';
include '../db.php';
include 'faculty_session.php';


$faculty_id = $_SESSION['faculty_id'];

// Fetch Assigned Subjects for Faculty
$subject_query = "SELECT s.subject_id, s.subject_name, s.student_sem_id, c.course_name
                  FROM subject s
                  JOIN course c ON s.course_id = c.course_id
                  WHERE s.subject_id IN (SELECT subject_id FROM faculty_subject WHERE faculty_id = $faculty_id)";
$subject_result = mysqli_query($conn, $subject_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Assigned Subjects</title>
    <link rel="stylesheet" href="./css/subjectList.css">

</head>

<body>



    <div class="main-content">
        <h2>📚 Assigned Subjects</h2>

        <table class="table subject-table">
            <tr>
                <th>Subject Name</th>
                <th>Course Name</th>
                <th>Semester</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($subject_result)) { ?>
            <tr>
                <td><?= $row['subject_name']; ?></td>
                <td><?= $row['course_name']; ?></td>
                <td><?= $row['student_sem_id']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <div class="fw-bold text-center fs-2 p-5">
            <<<...back to the <a href="faculty_dashboard.php">Dashboard</a>
        </div>
    </div>

</body>

</html>