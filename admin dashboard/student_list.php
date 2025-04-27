<?php

include '../link.php';
include '../db.php';

    $studentName = $selectedSem = $selectedCourse = "";

    $courseQuery = "SELECT DISTINCT course_id, course_name FROM course";
    $courseResult = $conn->query($courseQuery);
    
    $semesterQuery = "SELECT DISTINCT student_sem_id FROM student ORDER BY student_sem_id";
    $semesterResult = $conn->query($semesterQuery);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $studentName = $_POST["student_name"] ?? "";
        $selectedSem = $_POST["student_sem"] ?? "";
        $selectedCourse = $_POST["course_id"] ?? "";
    }
    
    $studentQuery = "SELECT * FROM student s 
                     INNER JOIN course c ON s.course_id = c.course_id 
                     WHERE s.student_status = 'active'";
    
    if (!empty($studentName)) {
        $studentQuery .= " AND s.student_name LIKE '%$studentName%'";
    }
    if (!empty($selectedSem)) {
        $studentQuery .= " AND s.student_sem_id = '$selectedSem'";
    }
    if (!empty($selectedCourse)) {
        $studentQuery .= " AND s.course_id = '$selectedCourse'";
    }
    
    $studentQuery .= " ORDER BY s.student_sem_id, s.student_id";
    $result = $conn->query($studentQuery);
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentList</title>
</head>
<body>
<div class="container mt-5">
        <h3 class="fw-bold">Search Students</h3>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="student_name" class="form-control" placeholder="Enter Student Name"
                        value="<?= htmlspecialchars($studentName) ?>">
                </div>
                <div class="col-md-3">
                    <select name="student_sem" class="form-control">
                        <option value="">Select Semester</option>
                        <?php while ($row = $semesterResult->fetch_assoc()): ?>
                        <option value="<?= $row['student_sem_id'] ?>"
                            <?= ($selectedSem == $row['student_sem_id']) ? 'selected' : '' ?>>
                            Semester <?= $row['student_sem_id'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-control">
                        <option value="">Select Course</option>
                        <?php while ($row = $courseResult->fetch_assoc()): ?>
                        <option value="<?= $row['course_id'] ?>"
                            <?= ($selectedCourse == $row['course_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['course_name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["student_id"] ?></td>
                    <td><?= htmlspecialchars($row["student_name"]) ?></td>
                    <td><?= htmlspecialchars($row["course_name"]) ?></td>
                    <td><?= $row["student_sem_id"] ?></td>
                    <td><?= ucfirst($row["student_gender"]) ?></td>
                    <td><?= date("d/m/Y", strtotime($row["student_birthdate"])) ?></td>
                    <td><?= htmlspecialchars($row["student_email"]) ?></td>
                    <td><?= ucfirst($row["student_status"]) ?></td>
                    <td><?= date("d-m-Y h:i A", strtotime($row["created_at"])) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No students found</td>
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
    