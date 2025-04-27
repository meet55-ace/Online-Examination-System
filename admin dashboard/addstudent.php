<?php
include '../db.php';
include '../link.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = trim($_POST['student_name']);
    $course_name = trim($_POST['course_id']);
    $gender = trim($_POST['student_gender']);
    $birthdate = trim($_POST['student_birthdate']);
    $semester_id = trim($_POST['student_sem_id']);
    $email = trim($_POST['student_email']);
    $password = password_hash(trim($_POST['student_password']), PASSWORD_BCRYPT);

    $stmt_check = $conn->prepare("SELECT * FROM student WHERE student_email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header("Location: addstudent.php?status=exists");
        exit();
    } else {
        $stmt_insert = $conn->prepare("
            INSERT INTO student (student_name, course_id, student_gender, student_birthdate, student_sem_id, student_email, student_password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt_insert->bind_param(
            "ssssiss",
            $student_name,
            $course_name,
            $gender,
            $birthdate,
            $semester_id,
            $email,
            $password
        );

        if ($stmt_insert->execute()) {
            header("Location: addstudent.php?status=success");
        } else {
            header("Location: addstudent.php?status=error");
        }
        exit();
    }
}
?>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Student added successfully.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });
        } else if (status === 'exists') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Student with this email already exists.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });

        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to add student.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });

        }
    });
</script>
