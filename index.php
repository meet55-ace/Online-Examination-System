<?php
include 'db.php';
include 'student dashboard/student_session.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_POST["student_email"];
    $password = $_POST["student_password"];

    // Use prepared statements to avoid SQL injection
    $query = "SELECT student_id, student_password, student_email FROM student WHERE LOWER(student_email) = LOWER(?)";
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        // Debugging: Print the stored password hash and the entered password
        echo "<script>console.log('Stored Hash: " . $student['student_password'] . "');</script>";
        echo "<script>console.log('Entered Password: " . $password . "');</script>";

        // Verify the password against the hash
        if (password_verify($password, $student['student_password'])) {
            // Set session variables
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_email'] = $student['student_email'];

            // Optionally, you can store more session data, such as the student's name, if needed
            // $_SESSION['student_name'] = $student['student_name'];  // If 'student_name' exists

            echo "<script>alert('Login Successful');</script>";

            // Redirect to the student dashboard
            header("Location: student dashboard/studentDashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid password'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Invalid email'); window.history.back();</script>";
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="studentStyle.css">
</head>

<body>
    <div class="title">ExamEase</div>
    <div class="box">
        <form method="POST">
            <h2>Student Login</h2>
            <div class="input-box">
                <input type="email" name="student_email" id="email" required>
                <span>Email</span>
                <i></i>
            </div>

            <div class="input-box">
                <input type="password" name="student_password" id="password" required>
                <span>Password</span>
                <i></i>
            </div>

            <input type="submit" value="login">
        </form>
    </div>
</body>

</html>