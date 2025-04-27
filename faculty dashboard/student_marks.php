<?php
include '../db.php';
include 'faculty_session.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch students assigned to this faculty's subject
$students_query = "SELECT DISTINCT s.student_id, s.student_name 
                   FROM student_answers sa
                   JOIN student s ON sa.student_id = s.student_id
                   JOIN question q ON sa.ques_id = q.question_id
                   WHERE q.subject_id IN (SELECT subject_id FROM faculty_subject WHERE faculty_id = ?)";

$stmt = mysqli_prepare($conn, $students_query);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$students_result = mysqli_stmt_get_result($stmt);
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Marks</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 20px;
        text-align: center;
    }

    h2 {
        color: #333;
    }

    label {
        font-weight: bold;
    }

    select {
        padding: 8px;
        margin: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .subject-container {
        width: 80%;
        margin: 20px auto;
        text-align: left;
    }

    table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        background-color: #3B1E54;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e0e0e0;
    }

    .no-data {
        color: red;
        font-weight: bold;
    }
    </style>
</head>

<body>

    <h2>Student Marks</h2>
    <label for="student">Select Student:</label>
    <select id="student" onchange="fetchStudentMarks(this.value)">
        <option value="">--Select--</option>
        <?php foreach ($students as $student) { ?>
        <option value="<?php echo $student['student_id']; ?>">
            <?php echo $student['student_name']; ?>
        </option>
        <?php } ?>
    </select>

    <div id="marksContainer"></div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="faculty_dashboard.php">Dashboard</a>
    </div>

    <script>
    function fetchStudentMarks(studentId) {
        console.log("Fetching marks for student ID:", studentId);

        if (!studentId) {
            document.getElementById("marksContainer").innerHTML = "<p class='no-data'>Please select a student.</p>";
            return;
        }

        fetch("fetch_student_marks.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `student_id=${studentId}&faculty_id=<?php echo $faculty_id; ?>`
            })
            .then(response => response.text()) // First, get raw response
            .then(data => {
                console.log("Raw Response:", data); // Log raw response

                try {
                    data = JSON.parse(data); // Now, parse JSON
                    console.log("Parsed JSON:", data);
                } catch (error) {
                    console.error("JSON Parsing Error:", error);
                    document.getElementById("marksContainer").innerHTML =
                        "<p class='no-data'>Error fetching data. Check PHP logs.</p>";
                    return;
                }

                // Display student marks
                displayStudentMarks(data);
            })
            .catch(error => console.error("Fetch Error:", error));
    }

    function displayStudentMarks(data) {
        let container = document.getElementById("marksContainer");
        container.innerHTML = "";

        if (Object.keys(data).length === 0) {
            container.innerHTML = "<p class='no-data'>No marks found for this student.</p>";
            return;
        }

        for (const subject in data) {
            let subjectData = data[subject];
            let subjectTable = `
    <div class="subject-container">
        <h3>${subject}</h3>
        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    
                    <th>Selected Answer</th>
                    <th>Correct Answer</th>
                    <th>Status</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
    `;

            subjectData.forEach(row => {
                subjectTable += `
        <tr>
            <td>${row.question}</td>
            
            <td>${row.options[row.selected_option]}</td>
            <td>${row.options[row.correct_option]}</td>
            <td>${row.student_answer_status}</td>
            <td>${row.marks_obtained} / ${row.ques_mark}</td>
        </tr>
        `;
            });

            subjectTable += `</tbody></table></div>`;
            container.innerHTML += subjectTable;
        }

    }
    </script>

</body>

</html>