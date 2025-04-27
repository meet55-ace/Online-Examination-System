<?php
include '../db.php';
include 'student_session.php';
// print_r($_SESSION);

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
} else {
    echo "Student ID not found. Please log in.";
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #f3f4f7, #e0e7ff);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, rgb(161, 144, 200), rgb(33, 85, 196));
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Container */
        .container {
            flex: 1;
            padding: 30px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Welcome Section */
        .welcome {
            font-size: 32px;
            margin-bottom: 30px;
            color: #1a202c;
            font-weight: 600;
        }

        /* Actions Section */
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding-bottom: 20px;
        }

        /* Action Card */
        .action-card {
            background: white;
            border-radius: 20px;
            padding: 15px;
            width: 350px;
            height: 350px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            transform: rotate(-2deg) scale(1.1);
            /* Rotate and scale */
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #e0e7ff, #cbd5e1);
            /* Change background on hover */
        }

        /* Adding a subtle motion blur effect */
        .action-card::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4), transparent);
            filter: blur(10px);
            opacity: 0;
            z-index: 0;
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .action-card:hover::before {
            opacity: 1;
            transform: scale(1.5);
        }

        /* Ensure content stays above hover effects */
        .action-card img,
        .action-card h3,
        .action-card p {
            position: relative;
            z-index: 1;
        }

        /* Card Image */
        .action-card img {
            width: 300px;
            height: 200px;
            margin-bottom: 15px;
            border-radius: 12px;
        }

        /* Card Title */
        .action-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #1a202c;
            font-weight: bold;
        }

        /* Card Description */
        .action-card p {
            font-size: 16px;
            color: #6b7280;
        }

        /* Logout Button */
        .logout {
            margin-top: 40px;
            padding: 12px 25px;
            background: linear-gradient(90deg, rgb(7, 43, 120), rgb(13, 123, 201));
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .logout:hover {
            background: linear-gradient(90deg, rgb(13, 123, 201), rgb(7, 43, 120));
            transform: scale(1.05);
        }

        .logout:active {
            transform: scale(1);
        }

        /* Media Query for Responsive Design */
        @media (max-width: 768px) {
            .action-card {
                width: 90%;
                height: auto;
                padding: 20px;
            }

            .action-card img {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">Student Dashboard</div>

    <div class="container">
        <div class="welcome">Welcome, <?php echo isset($student_email) ? htmlspecialchars($student_email) : 'Student'; ?>!</div>
        <div class="actions">
            <div class="action-card" onclick="window.location.href='examList.php'">
                <img src="startexam.png" alt="Start Exam">
                <h3>Start Exam</h3>
                <p>Take your upcoming exams here.</p>
            </div>
            <div class="action-card" onclick="window.location.href='viewResult.php'">
                <img src="viewres.jpeg" alt="View Results">
                <h3>View Results</h3>
                <p>Check your exam results.</p>
            </div>
            <div class="action-card" onclick="window.location.href='profile.php'">
                <img src="profile.jpeg" alt="My Profile">
                <h3>My Profile</h3>
                <p>View your profile information.</p>
            </div>
        </div>
        <button class="logout" onclick="window.location.href='../index.php'">Logout</button>
    </div>
</body>

</html>