<?php
include '../db.php'; // Database connection
include '../link.php'; // CSS & JS includes

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faculty_name = trim($_POST['faculty_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Check if faculty already exists
    $stmt_check = $conn->prepare("SELECT * FROM faculty WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header("Location: addfaculty.php?status=exists");
        exit();
    } else {
        // Insert faculty details (NO COURSE)
        $stmt_insert = $conn->prepare("INSERT INTO faculty (faculty_name, email, phone_number, password) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $faculty_name, $email, $phone_number, $password);

        if ($stmt_insert->execute()) {
            header("Location: addfaculty.php?status=success");
        } else {
            header("Location: addfaculty.php?status=error");
        }
        exit();
    }
}
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Faculty added successfully.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });
        } else if (status === 'exists') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Faculty with this email already exists.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to add faculty.',
            }).then(() => {
                window.location.href = 'dashboard.php'; 
            });
        }
    });
</script>
