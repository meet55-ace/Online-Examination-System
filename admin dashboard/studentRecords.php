<?php
include '../link.php';
include '../db.php';

// Fetch semesters
$semestersQuery = "SELECT DISTINCT student_sem_id FROM student ORDER BY student_sem_id";
$semestersResult = $conn->query($semestersQuery);

// Fetch courses
$coursesQuery = "SELECT DISTINCT course_id, course_name FROM course ORDER BY course_name";
$coursesResult = $conn->query($coursesQuery);

// Get selected filters
$selectedSem = isset($_GET['semester']) ? intval($_GET['semester']) : 0;
$selectedCourse = isset($_GET['course']) ? intval($_GET['course']) : 0;

// Query to fetch present and absent students
$completedQuery = "
    SELECT 
        s.student_id,
        s.student_name, 
        c.course_name, 
        s.student_sem_id, 
        e.exam_title, 
        COALESCE(ea.status, 'Absent') AS status
    FROM student s
    JOIN course c ON s.course_id = c.course_id
    JOIN exam e ON e.course_id = c.course_id 
    LEFT JOIN exam_attempt ea ON s.student_id = ea.student_id AND e.exam_id = ea.exam_id
    WHERE e.student_sem_id = s.student_sem_id"; // Ensures only relevant semester exams are shown

// Apply filters only if selected
if ($selectedSem > 0) {
    $completedQuery .= " AND s.student_sem_id = $selectedSem";
}
if ($selectedCourse > 0) {
    $completedQuery .= " AND s.course_id = $selectedCourse";
}

$completedQuery .= " ORDER BY s.student_sem_id, s.student_id"; 

$completedResult = $conn->query($completedQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Attendance Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 pt-5">
        <h3 class="fw-bold fs-2">Exam Attendance Report</h3><br>

        <form method="GET" action="" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="semester" class="form-label">Select Semester:</label>
                    <select name="semester" id="semester" class="form-select">
                        <option value="0">All Semesters</option>
                        <?php while ($row = $semestersResult->fetch_assoc()) { ?>
                            <option value="<?= $row['student_sem_id']; ?>" 
                                <?= ($selectedSem == $row['student_sem_id']) ? 'selected' : ''; ?>>
                                Semester <?= $row['student_sem_id']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="course" class="form-label">Select Course:</label>
                    <select name="course" id="course" class="form-select">
                        <option value="0">All Courses</option>
                        <?php while ($row = $coursesResult->fetch_assoc()) { ?>
                            <option value="<?= $row['course_id']; ?>" 
                                <?= ($selectedCourse == $row['course_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($row['course_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Exam Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($completedResult->num_rows > 0): ?>
                    <?php while ($row = $completedResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['student_id']; ?></td>
                            <td><?= htmlspecialchars($row['student_name']); ?></td>
                            <td><?= htmlspecialchars($row['course_name']); ?></td>
                            <td><?= $row['student_sem_id']; ?></td>
                            <td><?= htmlspecialchars($row['exam_title']); ?></td>
                            <td class="<?= $row['status'] == 'Present' ? 'text-success fw-bold' : 'text-danger fw-bold'; ?>">
                                <?= $row['status']; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No students found</td>
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
