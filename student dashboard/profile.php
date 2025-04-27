<?php
include '../db.php';
include 'student_session.php';

// Check if the student is logged in
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
} else {
    echo "Student ID not found. Please log in.";
    exit();
}

// Fetch student details from the database
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "Student details not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
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

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-card h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #1a202c;
        }

        .profile-card .detail {
            margin-bottom: 15px;
            font-size: 18px;
            color: #4a5568;
        }

        .profile-card .detail strong {
            color: #1a202c;
        }

        /* Edit Button */
        .edit-button {
            margin-top: 20px;
            padding: 12px 25px;
            background: linear-gradient(90deg,rgb(15, 93, 161),rgb(164, 24, 214));
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .edit-button:hover {
            background: linear-gradient(90deg,rgb(164, 24, 214),rgb(15, 93, 161));
            transform: scale(1.05);
        }

        .edit-button:active {
            transform: scale(1);
        }
    </style>
</head>

<body>
    <br><br><br><br><br><br><br><br>
    <div class="profile-card">
        <br><br>
        <h2>Student Profile</h2>
        <br>
        <div class="detail"><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></div>
        <div class="detail"><strong>Name:</strong> <?php echo htmlspecialchars($student['student_name']); ?></div>
        <div class="detail"><strong>Email:</strong> <?php echo htmlspecialchars($student['student_email']); ?></div>
        <div class="detail"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student['student_birthdate']); ?></div>
        <div class="detail"><strong>Student Gender:</strong> <?php echo htmlspecialchars($student['student_gender']); ?></div>
        <button class="edit-button" onclick="window.location.href='studentDashboard.php'">OK</button>
    </div>
</body>

</html>
