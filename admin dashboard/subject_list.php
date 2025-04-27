<?php

include '../link.php';
include '../db.php';

$filterCourse = $filterSemester = "";

// Fetch all courses
$getCoursesQuery = "SELECT DISTINCT course_id, course_name FROM course";
$courseList = $conn->query($getCoursesQuery);

// Fetch all semesters
$getSemestersQuery = "SELECT DISTINCT student_sem_id FROM student ORDER BY student_sem_id";
$semesterList = $conn->query($getSemestersQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filterCourse = $_POST["filter_course"] ?? "";
    $filterSemester = $_POST["filter_semester"] ?? "";
}

// Fetch subjects based on selected course and semester
$getSubjectsQuery = "SELECT DISTINCT subject_id, subject_name FROM subject WHERE 1";
if (!empty($filterCourse)) {
    $getSubjectsQuery .= " AND course_id = '$filterCourse'";
}
if (!empty($filterSemester)) {
    $getSubjectsQuery .= " AND student_sem_id = '$filterSemester'";
}
$getSubjectsQuery .= " ORDER BY subject_name";
$subjectList = $conn->query($getSubjectsQuery);

    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubjectList</title>
</head>

<body>
    <div class="container mt-5">
        <h3 class="fw-bold">Filter Subjects</h3>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="filter_course" class="form-control">
                        <option value="">Select Course</option>
                        <?php while ($courseRow = $courseList->fetch_assoc()): ?>
                        <option value="<?= $courseRow['course_id'] ?>"
                            <?= ($filterCourse == $courseRow['course_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($courseRow['course_name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="filter_semester" class="form-control">
                        <option value="">Select Semester</option>
                        <?php while ($semRow = $semesterList->fetch_assoc()): ?>
                        <option value="<?= $semRow['student_sem_id'] ?>"
                            <?= ($filterSemester == $semRow['student_sem_id']) ? 'selected' : '' ?>>
                            Semester <?= $semRow['student_sem_id'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Subject ID</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($subjectList->num_rows > 0): ?>
                <?php while ($subjectRow = $subjectList->fetch_assoc()): ?>
                <tr>
                    <td><?= $subjectRow["subject_id"] ?></td>
                    <td><?= htmlspecialchars($subjectRow["subject_name"]) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">No subjects found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>
</body>

</html>