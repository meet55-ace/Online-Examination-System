<?php
include 'student_session.php'; 
include '../db.php'; 

if (!isset($_SESSION['student_id'])) {
    die("Error: You must log in to view this page.");
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$query = "SELECT s.student_name, s.student_birthdate, c.course_name, sem.sem_name 
          FROM student s
          JOIN course c ON s.course_id = c.course_id
          JOIN student_sem sem ON s.student_sem_id = sem.student_sem_id
          WHERE s.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Error: No student found with the provided ID.");
}

// Fetch individual exam results with subject name
$result_sql = "SELECT sub.subject_name, se.exam_marks_obtained, se.exam_max_marks
               FROM student_exam_results se
               JOIN exam e ON se.exam_id = e.exam_id
               JOIN subject sub ON e.subject_id = sub.subject_id
               WHERE se.student_id = ?";

$stmt = $conn->prepare($result_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$exam_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch semester-wise result from student_results table
$sem_result_sql = "SELECT max_marks, marks_obtained, percentage, status
                   FROM student_results
                   WHERE student_id = ?";
$stmt = $conn->prepare($sem_result_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$sem_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();

// Initialize total values
$total_max_marks = 0;
$total_marks_obtained = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result</title>
    <link rel="stylesheet" href="css/viewResult.css">
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 10px;
    }

    th {
        background-color: orangered;
        color: white;
    }

    .bold {
        font-weight: bold;
    }

    .percentage {
        text-align: center;
        margin-top: 10px;
        font-weight: bold;
        font-size: 18px;
    }
    .download-container {
            text-align: center;
        }
        .download-btn {
            background-color: rgb(83, 152, 242);;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
        .download-btn:hover {
            background-color:rgb(9, 88, 192);
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="header">
        <!-- <img src="../student dashboard/ksv.png" alt="Logo"> -->
        <h1>Exam Ease</h1>
    </div>

    <div class="result-container">
        <div class="student-info">
            <div class="left-info">
                <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
                <p><strong>Student Name:</strong> <?php echo htmlspecialchars($student['student_name']); ?></p>
                <p><strong>DOB:</strong> <?php echo htmlspecialchars($student['student_birthdate']); ?></p>
            </div>

            <div class="right-info">
                <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course_name']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($student['sem_name']); ?></p>
            </div>
        </div>

        <?php if (empty($exam_results)) { ?>
        <div class="no-results">
            <h2>Result Awaited</h2>
            <p>Your results have not been published yet. Please check back later.</p>
        </div>
        <?php } else { ?>
        <table>
            <thead>
                <tr>
                    <th>SUBJECT NAME</th>
                    <th>MAX MARKS</th>
                    <th>MARKS OBTAINED</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exam_results as $row) { 
                        $total_max_marks += $row['exam_max_marks'];
                        $total_marks_obtained += $row['exam_marks_obtained'];
                    ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_max_marks']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_marks_obtained']); ?></td>
                </tr>
                <?php } ?>
                <tr class="bold">
                    <td>Total</td>
                    <td><?php echo $total_max_marks; ?></td>
                    <td><?php echo $total_marks_obtained; ?></td>
                </tr>
            </tbody>
        </table>

        <div class="percentage">
            <p><strong>Percentage:</strong> <?php echo number_format($sem_result['percentage'], 2) . "%"; ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($sem_result['status']); ?></p>
        </div>
        <?php } ?>

        <div class="download-container">
            <form action="downloadresult.php" method="post">
                <button type="submit" class="download-btn">Download PDF</button>
            </form>
        </div>

    </div>
    <div class="back-link">
        <<<...back to the <a href="studentDashboard.php">Dashboard</a>
    </div>
</body>

</html>