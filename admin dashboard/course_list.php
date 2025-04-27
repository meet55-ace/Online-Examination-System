<?php

include '../link.php';
include '../db.php';


$filterCourse = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filterCourse = $_POST["filter_course"] ?? "";
}

$getCoursesQuery = "SELECT course_id, course_name, created_at FROM course";
if (!empty($filterCourse)) {
    $getCoursesQuery .= " WHERE course_name LIKE '%$filterCourse%'";
}
$getCoursesQuery .= " ORDER BY course_name";

$courseList = $conn->query($getCoursesQuery);
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseList</title>
</head>

<body>
    <div class="container mt-5">
        <h3 class="fw-bold">Course List</h3>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="filter_course" class="form-control" placeholder="Search Course Name"
                        value="<?= htmlspecialchars($filterCourse) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($courseList->num_rows > 0): ?>
                <?php while ($row = $courseList->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["course_id"] ?></td>
                    <td><?= htmlspecialchars($row["course_name"]) ?></td>
                    <td><?= date("d-m-Y h:i A", strtotime($row["created_at"])) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No courses found</td>
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