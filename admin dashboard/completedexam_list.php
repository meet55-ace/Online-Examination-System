<?php
include '../db.php';
include '../link.php';

// Fetch course and semester filter values from GET request
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Build query conditions
$whereConditions = ["exam.exam_status = 'Completed'"]; // Only completed exams
$params = [];
$types = "";

// Apply course filter if selected
if (!empty($selectedCourse)) {
    $whereConditions[] = "exam.course_id = ?";
    $params[] = $selectedCourse;
    $types .= "i";
}

// Apply semester filter if selected
if (!empty($selectedSemester)) {
    $whereConditions[] = "exam.student_sem_id = ?";
    $params[] = $selectedSemester;
    $types .= "i";
}

// Construct the main SQL query (JOIN with course & subject table to get course_name & subject_name)
$examQuery = "SELECT exam.*, course.course_name, subject.subject_name 
              FROM exam 
              JOIN course ON exam.course_id = course.course_id
              JOIN subject ON exam.subject_id = subject.subject_id";

// Append WHERE conditions
if (!empty($whereConditions)) {
    $examQuery .= " WHERE " . implode(" AND ", $whereConditions);
}

// Prepare and execute query
$examStmt = $conn->prepare($examQuery);
if (!empty($params)) {
    $examStmt->bind_param($types, ...$params);
}
$examStmt->execute();
$exams = $examStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Exams</title>
    <link rel="stylesheet" href="./css/examList.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="fw-bold text-center">Completed Exams</h3>

        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <label for="course" class="fw-bold">Select Course:</label>
                    <select name="course" id="course" class="form-control">
                        <option value="">All Courses</option>
                        <?php
                        $courseQuery = "SELECT DISTINCT course.course_id, course.course_name 
                                        FROM exam 
                                        JOIN course ON exam.course_id = course.course_id 
                                        WHERE exam.exam_status = 'Completed'";
                        $courseResult = $conn->query($courseQuery);
                        while ($row = $courseResult->fetch_assoc()) {
                            $selected = ($selectedCourse == $row['course_id']) ? 'selected' : '';
                            echo "<option value='{$row['course_id']}' $selected>{$row['course_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-5">
                    <label for="semester" class="fw-bold">Select Semester:</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <?php
                        $semQuery = "SELECT DISTINCT student_sem_id FROM exam WHERE exam_status = 'Completed'";
                        $semResult = $conn->query($semQuery);
                        while ($row = $semResult->fetch_assoc()) {
                            $selected = ($selectedSemester == $row['student_sem_id']) ? 'selected' : '';
                            echo "<option value='{$row['student_sem_id']}' $selected>Semester {$row['student_sem_id']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Display Exam Table -->
        <?php if ($exams && mysqli_num_rows($exams) > 0) { ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Exam Title</th>
                        <th>Course Name</th>
                        <th>Subject</th>
                        <th>Semester</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($exam = mysqli_fetch_assoc($exams)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['exam_title']); ?></td>
                            <td><?= htmlspecialchars($exam['course_name']); ?></td>
                            <td><?= htmlspecialchars($exam['subject_name']); ?></td>
                            <td>Semester <?= htmlspecialchars($exam['student_sem_id']); ?></td>
                            <td><?= date('d-m-Y h:i A', strtotime($exam['exam_start_time'])); ?></td>
                            <td><?= date('d-m-Y h:i A', strtotime($exam['exam_end_time'])); ?></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($exam['exam_status']); ?></span></td>
                            <td>
                                <a href="view.php?exam_id=<?= $exam['exam_id']; ?>" class="btn btn-info btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-warning text-center">No completed exams available.</div>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><<< Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
