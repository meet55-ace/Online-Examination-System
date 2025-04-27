<?php
include '../link.php';
include '../db.php';
include 'faculty_session.php';
// include 'navbar.php';
// include 'sidebar.php';

$faculty_id = $_SESSION['faculty_id'];

// Fetch Assigned Subjects for Faculty
$subject_query = "SELECT s.subject_id, s.subject_name, s.student_sem_id, c.course_id, c.course_name 
                  FROM subject s 
                  JOIN course c ON s.course_id = c.course_id
                  WHERE s.subject_id IN (SELECT subject_id FROM faculty_subject WHERE faculty_id = $faculty_id)";
$subject_result = mysqli_query($conn, $subject_query);

$subject_data = [];
$semester_ids = [];

while ($row = mysqli_fetch_assoc($subject_result)) {
    $subject_data[] = $row;
    $semester_ids[] = $row['student_sem_id'];
}

$semester_ids_str = implode(',', array_unique($semester_ids));

if (!empty($semester_ids)) {
    $student_query = "SELECT s.student_id, s.student_name, s.student_sem_id, s.course_id, c.course_name 
                      FROM student s
                      JOIN course c ON s.course_id = c.course_id
                      WHERE s.student_sem_id IN ($semester_ids_str)";
    $student_result = mysqli_query($conn, $student_query);
} else {
    $student_result = false;
}

$students_by_semester = [];
if ($student_result && mysqli_num_rows($student_result) > 0) {
    while ($student = mysqli_fetch_assoc($student_result)) {
        $students_by_semester[$student['student_sem_id']][] = $student;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Assigned Students</title>
    <link rel="stylesheet" href="./css/studentList.css">

</head>

<body>

    <div class="main-content">
        <h2>📚 Students Assigned to Your Subjects</h2>

        <?php foreach ($subject_data as $subject) { ?>
        <h3 class="subject-heading"><?= $subject['subject_name']; ?> (Semester <?= $subject['student_sem_id']; ?> -
            <?= $subject['course_name']; ?>)</h3>
        <table class="table student-table">
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <!-- <th>Course</th>
                    <th>Semester</th> -->
            </tr>
            <?php if (!empty($students_by_semester[$subject['student_sem_id']])) {
                    foreach ($students_by_semester[$subject['student_sem_id']] as $student) { ?>
            <tr>
                <td><?= $student['student_id']; ?></td>
                <td><?= $student['student_name']; ?></td>
                <!-- <td><?= $student['course_name']; ?></td> -->
                <!-- <td><?= $student['student_sem_id']; ?></td> -->
            </tr>
            <?php }
                } else { ?>
            <tr>
                <td colspan="4">No students found.</td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        <div class="fw-bold text-center fs-2 p-5">
            <<<...back to the <a href="faculty_dashboard.php">Dashboard</a>
        </div>
    </div>

</body>

</html>