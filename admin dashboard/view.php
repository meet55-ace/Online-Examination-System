<?php
include '../db.php';
include '../link.php';

if (!isset($_GET['exam_id'])) {
    echo "<script>alert('Exam ID is missing!'); window.location.href='examList.php';</script>";
    exit;
}

$exam_id = $_GET['exam_id'];

// Fetch Exam Questions
$questionQuery = "SELECT * FROM question WHERE exam_id = ?";
$questionStmt = $conn->prepare($questionQuery);
$questionStmt->bind_param("i", $exam_id);
$questionStmt->execute();
$questions = $questionStmt->get_result();

// Fetch Students and their Exam Status (Present/Absent)
$studentQuery = "SELECT e.student_id, s.student_name, e.status 
                 FROM exam_attempt e 
                 JOIN student s ON e.student_id = s.student_id 
                 WHERE e.exam_id = ?";
$studentStmt = $conn->prepare($studentQuery);
$studentStmt->bind_param("i", $exam_id);
$studentStmt->execute();
$students = $studentStmt->get_result();

$presentStudents = [];
$absentStudents = [];

while ($student = $students->fetch_assoc()) {
    if ($student['status'] === 'Present' || $student['status'] === 'Completed') {
        $presentStudents[] = $student;
    } else {
        $absentStudents[] = $student;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Exam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Questions List -->
        <h4 class="fw-bold text-center">Exam Questions</h4>
        <div class="card p-3 shadow mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Question</th>
                        <th>Option 1</th>
                        <th>Option 2</th>
                        <th>Option 3</th>
                        <th>Option 4</th>
                        <th>Correct Answer</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($question = $questions->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($question['question']); ?></td>
                            <td><?= htmlspecialchars($question['op1']); ?></td>
                            <td><?= htmlspecialchars($question['op2']); ?></td>
                            <td><?= htmlspecialchars($question['op3']); ?></td>
                            <td><?= htmlspecialchars($question['op4']); ?></td>
                            <td class="text-success"><?= htmlspecialchars($question['correct_option']); ?></td>
                            <td><?= htmlspecialchars($question['ques_mark']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Students Present and Absent -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow p-3 mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 text-center">Students Present</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($presentStudents)) { 
                                    foreach ($presentStudents as $student) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['student_id']); ?></td>
                                            <td><?= htmlspecialchars($student['student_name']); ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($student['status']); ?></span></td>
                                        </tr>
                                    <?php } 
                                } else { ?>
                                    <tr><td colspan="3" class="text-center text-muted">No students were present.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow p-3 mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0 text-center">Students Absent</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($absentStudents)) { 
                                    foreach ($absentStudents as $student) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['student_id']); ?></td>
                                            <td><?= htmlspecialchars($student['student_name']); ?></td>
                                            <td><span class="badge bg-danger"><?= htmlspecialchars($student['status']); ?></span></td>
                                        </tr>
                                    <?php } 
                                } else { ?>
                                    <tr><td colspan="3" class="text-center text-muted">No students were absent.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="completedexam_list.php" class="btn btn-secondary">&laquo; Back to Exam List</a>
        </div>
    </div>
</body>
</html>
