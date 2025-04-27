<?php
include '../link.php';
include '../db.php';

if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];
    
    $sql = "SELECT exam.*, course.course_name, student_sem.sem_name, subject.subject_name 
            FROM exam 
            LEFT JOIN course ON exam.course_id = course.course_id
            LEFT JOIN student_sem ON exam.student_sem_id = student_sem.student_sem_id
            LEFT JOIN subject ON exam.subject_id = subject.subject_id
            WHERE exam.exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $exam = $result->fetch_assoc();
    } else {
        echo "Exam not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_title = $_POST['exam_title'];
    $course_id = $_POST['course_id'];
    $student_sem_id = $_POST['student_sem_id'];
    $subject_id = $_POST['subject_id'];
    $question_limit = $_POST['question_limit'];
    $duration_min = $_POST['duration_min'];
    $exam_start_time = $_POST['exam_start_time'];
    $exam_marks = $_POST['exam_marks'];

    $update_sql = "UPDATE exam SET exam_title = ?, course_id = ?, student_sem_id = ?, subject_id = ?, 
                   question_limit = ?, duration_min = ?, exam_start_time = ?, exam_marks = ? 
                   WHERE exam_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("siiisdisi", $exam_title, $course_id, $student_sem_id, $subject_id, $question_limit, $duration_min, $exam_start_time, $exam_marks, $exam_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Exam updated successfully'); window.location.href='manageexam.php';</script>";
    } else {
        echo "<script>alert('Error updating exam');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="border p-3 rounded">
                    <h4>Update Exam</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="exam_title" class="form-label">Exam Title</label>
                            <input type="text" class="form-control" id="exam_title" name="exam_title" value="<?php echo htmlspecialchars($exam['exam_title']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <?php
                                $courses_sql = "SELECT * FROM course";
                                $courses_result = $conn->query($courses_sql);
                                while ($course = $courses_result->fetch_assoc()) {
                                    echo "<option value='" . $course['course_id'] . "'" . ($exam['course_id'] == $course['course_id'] ? ' selected' : '') . ">" . htmlspecialchars($course['course_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="student_sem_id" class="form-label">Semester</label>
                            <select class="form-select" id="student_sem_id" name="student_sem_id" required>
                                <?php
                                $semesters_sql = "SELECT * FROM student_sem";
                                $semesters_result = $conn->query($semesters_sql);
                                while ($semester = $semesters_result->fetch_assoc()) {
                                    echo "<option value='" . $semester['student_sem_id'] . "'" . ($exam['student_sem_id'] == $semester['student_sem_id'] ? ' selected' : '') . ">" . htmlspecialchars($semester['sem_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <?php
                                $subjects_sql = "SELECT * FROM subject";
                                $subjects_result = $conn->query($subjects_sql);
                                while ($subject = $subjects_result->fetch_assoc()) {
                                    echo "<option value='" . $subject['subject_id'] . "'" . ($exam['subject_id'] == $subject['subject_id'] ? ' selected' : '') . ">" . htmlspecialchars($subject['subject_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="question_limit" class="form-label">Question Limit</label>
                            <input type="number" class="form-control" id="question_limit" name="question_limit" value="<?php echo htmlspecialchars($exam['question_limit']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="duration_min" class="form-label">Duration (in minutes)</label>
                            <input type="number" class="form-control" id="duration_min" name="duration_min" value="<?php echo htmlspecialchars($exam['duration_min']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="exam_start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="exam_start_time" name="exam_start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($exam['exam_start_time'])); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="exam_marks" class="form-label">Exam Marks</label>
                            <input type="number" class="form-control" id="exam_marks" name="exam_marks" value="<?php echo htmlspecialchars($exam['exam_marks']); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success">Update Exam</button>
                        <a href="manageexam.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

            
        </div>
    </div>
</body>

</html>