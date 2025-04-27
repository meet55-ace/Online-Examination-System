<?php
include '../db.php'; 
include '../link.php'; 

$exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;
if (!$exam_id) {
    header("Location: manageexam.php?error=No exam selected to edit.");
    exit;
}

// Fetch exam details from database
$query = "SELECT * FROM exam WHERE exam_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

if (!$exam) {
    header("Location: manageexam.php?error=Exam not found.");
    exit;
}

$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_title = $_POST['exam_title'];
    $question_limit = $_POST['question_limit'];
    $exam_start_time = $_POST['exam_start_time'];
    $exam_end_time = $_POST['exam_end_time'];
    // $exam_marks = $_POST['exam_marks'];

    $course_id = $exam['course_id'];
    $student_sem_id = $exam['student_sem_id'];
    $subject_id = $exam['subject_id'];

    $update_query = "UPDATE exam SET exam_title = ?, course_id = ?, student_sem_id = ?, subject_id = ?, question_limit = ?, exam_start_time = ?, exam_end_time = ? WHERE exam_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("siiiissi", $exam_title, $course_id, $student_sem_id, $subject_id, $question_limit, $exam_start_time, $exam_end_time, $exam_id);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: editexam.php?exam_id=$exam_id&success=1");
        exit;
    } else {
        $successMessage = "error";
    }
}

// Check if success message is set
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "success";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<!-- SweetAlert Success Message -->
<?php if ($successMessage === "success") { ?>
    <script>
        Swal.fire({
            title: "Success!",
            text: "Exam updated successfully.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php } ?>

<!-- SweetAlert Error Message -->
<?php if ($successMessage === "error") { ?>
    <script>
        Swal.fire({
            title: "Error!",
            text: "Failed to update the exam.",
            icon: "error"
        });
    </script>
<?php } ?>

<div class="container mt-5">
    <h3 class="fw-bold mb-4">Edit Exam</h3>
    <form method="POST" class="border p-4 rounded">
        <div class="mb-3">
            <label for="exam_title" class="form-label">Exam Title</label>
            <input type="text" id="exam_title" name="exam_title" class="form-control" value="<?php echo htmlspecialchars($exam['exam_title']); ?>" required>
        </div>

        <input type="hidden" name="course_id" value="<?php echo $exam['course_id']; ?>">
        <input type="hidden" name="student_sem_id" value="<?php echo $exam['student_sem_id']; ?>">
        <input type="hidden" name="subject_id" value="<?php echo $exam['subject_id']; ?>">

        <div class="mb-3">
            <label for="question_limit" class="form-label">Question Limit</label>
            <input type="number" id="question_limit" name="question_limit" class="form-control" value="<?php echo htmlspecialchars($exam['question_limit']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="exam_start_time" class="form-label">Start Time</label>
            <input type="datetime-local" id="exam_start_time" name="exam_start_time" class="form-control" value="<?php echo htmlspecialchars($exam['exam_start_time']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="exam_end_time" class="form-label">End Time</label>
            <input type="datetime-local" id="exam_end_time" name="exam_end_time" class="form-control" value="<?php echo htmlspecialchars($exam['exam_end_time']); ?>" required>
        </div>

        <!-- <div class="mb-3">
            <label for="exam_marks" class="form-label">Exam Marks</label>
            <input type="number" id="exam_marks" name="exam_marks" class="form-control" value="<?php echo htmlspecialchars($exam['exam_marks']); ?>" required>
        </div> -->

        <div class="d-flex">
            <button type="submit" class="btn btn-success">Update Exam</button>
            <a href="manageexam.php" class="btn btn-secondary">Cancel</a>
            <a href="addQuestion.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-dark">Add New Question</a>
        </div>
    </form>
</div>

<div class="fw-bold text-center fs-2 p-5">
    <<<...back to the <a href="dashboard.php">Dashboard</a>
</div>

</body>
</html>
