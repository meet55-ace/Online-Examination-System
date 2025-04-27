<?php
include '../link.php';
include '../db.php';
include 'faculty_session.php';

// Fetch Faculty Name
$faculty_id = $_SESSION['faculty_id'];
$query = "SELECT faculty_name FROM faculty WHERE faculty_id = $faculty_id";
$result = mysqli_query($conn, $query);
$faculty = mysqli_fetch_assoc($result);
$faculty_name = $faculty['faculty_name'];

// Fetch Pending Exams Assigned to Faculty
$exam_query = "SELECT exam.exam_id, exam.exam_title, exam.exam_start_time, exam.exam_end_time, subject.subject_id 
               FROM exam 
               JOIN subject ON exam.subject_id = subject.subject_id
               WHERE subject.subject_id IN 
               (SELECT subject_id FROM faculty_subject WHERE faculty_id = $faculty_id)
               AND exam.exam_end_time > NOW()";

$exam_result = mysqli_query($conn, $exam_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="./css/dashboard.css">
</head>

<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="ri-menu-line"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link"><?= $faculty_name; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="ri-logout-box-line fw-bold"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="d-lg-none p-2 bg-dark sidebar-toggler">
        <i class="ri-menu-line"></i>
    </div>

    <aside class="sidebar">
        <div class="header">
            <!-- <img src="../student dashboard/ksv.png" alt=""> -->
            <h1>EXAM EASE</h1>
        </div>
        <nav>
            <a>
                <i class="ri-book-line"></i>
                <p class="fw-bold">Manage Exams</p>
                <a href="previousExam.php" class="edit"><i class="ri-list-check"></i> Previous Exams</a>
            </a>
            <a>
                <i class="ri-user-line"></i>
                <p class="fw-bold">Manage Students</p>
                <a href="studentList.php" class="edit"><i class="ri-user-follow-fill"></i> Student List</a>
            </a>
            <a>
                <i class="ri-bar-chart-line"></i>
                <p class="fw-bold">Student Marks</p>
                <a href="student_marks.php" class="edit"><i class="ri-file-list-2-fill"></i> View Marks</a>
            </a>
            <a>
                <i class="fa-solid fa-book"></i>
                <p class="fw-bold">Subjects</p>
                <a href="subjectList.php" class="edit"><i class="fa-regular fa-rectangle-list"></i>Subject List</a>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-content">
            <h2>Pending Exams</h2>
            <table class="table">
                <tr>
                    <th>Exam Title</th>
                    <th>Exam Date</th> <!-- ✅ New Column for Date -->
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
                <?php if (mysqli_num_rows($exam_result) > 0) {
        while ($exam = mysqli_fetch_assoc($exam_result)) { ?>
                <tr>
                    <td><?= htmlspecialchars($exam['exam_title']); ?></td>
                    <td><?= date("d M Y", strtotime($exam['exam_start_time'])); ?></td> <!-- ✅ Extracting Date -->
                    <td><?= date("h:i A", strtotime($exam['exam_start_time'])); ?></td>
                    <td><?= date("h:i A", strtotime($exam['exam_end_time'])); ?></td>
                    <td>
                        <a href="add_questions.php?exam_id=<?= $exam['exam_id']; ?>&subject_id=<?= $exam['subject_id']; ?>"
                            class="btn">
                            Add Questions
                        </a>
                    </td>
                </tr>
                <?php } 
    } else { ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No pending exams.</td>
                </tr>
                <?php } ?>
            </table>

        </div>
    </div>

    <script src="dashboard.js"></script>
</body>

</html>