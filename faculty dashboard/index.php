<?php
include '../db.php';
include 'faculty_session.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST['faculty_identifier']);
    $password = trim($_POST['faculty_password']);

    if (empty($identifier) || empty($password)) {
        echo "<script>alert('Both fields are required!'); window.history.back();</script>";
        exit();
    }

    // Query to check faculty login via email or phone number
    $query = "SELECT faculty_id, faculty_name, email, password FROM faculty WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $faculty = $result->fetch_assoc();

        if (password_verify($password, $faculty['password'])) {
            $_SESSION['faculty_id'] = $faculty['faculty_id'];
            $_SESSION['faculty_name'] = $faculty['faculty_name'];
            $_SESSION['faculty_email'] = $faculty['email'];

            echo "<script>alert('Login Successful'); window.location.href='faculty_dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid Password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No faculty found with this Email or Phone Number!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Login</title>
   <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div class="title">ExamEase Faculty Page</div>
    <div class="login-container">
        <h1>Faculty Login</h1>
        <form  method="POST">
            <div class="form-group">
                <label>Email or Phone:</label>
                <input type="text" name="faculty_identifier" placeholder="Enter Email or Phone" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="faculty_password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>