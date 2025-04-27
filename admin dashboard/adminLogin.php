<?php
// include 'link.php';
include '../db.php';
include './admin_session.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST["admin_username"];
    $password = $_POST["admin_password"];

    $query = "SELECT admin_password FROM admin_login WHERE admin_username = '$username'";
    $result = mysqli_query($conn, $query);

    $admin = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        if ($password === $admin['admin_password']) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $username;
            echo "<script>alert('Login Successful');</script>";
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid username or password.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Invalid username or password.');  window.history.back();</script>";
        exit;
    }
}
// $adminUsername = $_SESSION['admin_username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            color: #fff;
            text-align: center;
        }

        .hero-section {
            position: absolute;
            top: 10%;
            width: 100%;
            z-index: -1;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            animation: slideIn 1.5s ease-out, glow 3s infinite alternate;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 50px;
            animation: slideIn 1.7s ease-out;
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-100px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes glow {
            0%,
            100% {
                text-shadow: 0 0 10px #fff, 0 0 20px #6a11cb, 0 0 30px #2575fc;
            }

            50% {
                text-shadow: 0 0 20px #fff, 0 0 40px #6a11cb, 0 0 60px #2575fc;
            }
        }

        .cardi {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 380px;
            text-align: left;
        }

        h2 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        .form-label {
            font-size: 1.1rem;
            color: rgba(255, 255, 255);
        }

        .form-control {
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2575fc;
            box-shadow: 0 0 10px rgba(37, 117, 252, 0.5);
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-primary {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            border: none;
            color: #fff;
            font-size: 1.2rem;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            transform: scale(1.05);
        }

        .d-grid {
            margin-top: 30px;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255);
            font-size: 1rem;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #2575fc;
        }

        /* Animations for Card Appearance */
        @keyframes cardFadeIn {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .cardi {
            animation: cardFadeIn 1s ease-out;
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <h1 class="hero-title">Welcome to ExamEase</h1>
        <p class="hero-subtitle">Simplify Your Online Examination Experience</p>
    </div>

    <div class="cardi">
        <h2>Admin Login</h2>
        <form method="POST" action="dashboard.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" id="admin_username" name="admin_username" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn-primary">Login</button>
            </div>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 ExamEase. All rights reserved.</p>
    </div>
</body>

</html>
