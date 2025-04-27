<?php

include '../db.php';
include '../link.php';

// Fetch exam attempts with exam and student details
$attempt_query = "
    SELECT ea.exam_attempt_id, e.exam_title, s.student_name, ea.status, ea.attempt_at
    FROM exam_attempt ea
    INNER JOIN exam e ON ea.exam_id = e.exam_id
    INNER JOIN student s ON ea.student_id = s.student_id
";
$result_attempt = $conn->query($attempt_query);

if ($result_attempt->num_rows === 0) {
    echo "<p>No exam attempts found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Attempts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background: #f4f4f9;
        }
        .view-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-btn:hover {
            background-color: #0056b3;
        }
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .close-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Exam Attempts</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Exam Name</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Attempt At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counter = 1;
            while ($row = $result_attempt->fetch_assoc()) { ?>
                <tr>
                    <td><?= $counter++; ?></td>
                    <td><?= htmlspecialchars($row['exam_title']); ?></td>
                    <td><?= htmlspecialchars($row['student_name']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td><?= htmlspecialchars($row['attempt_at']); ?></td>
                    <td>
                        <button class="view-btn" onclick="openModal(<?= htmlspecialchars(json_encode($row)); ?>)">View Data</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Modal for viewing details -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2>Exam Attempt Details</h2>
            <p><strong>Exam Name:</strong> <span id="modal-exam-title"></span></p>
            <p><strong>Student Name:</strong> <span id="modal-student-name"></span></p>
            <p><strong>Status:</strong> <span id="modal-status"></span></p>
            <p><strong>Attempt At:</strong> <span id="modal-attempt-at"></span></p>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal');

        function openModal(data) {
            document.getElementById('modal-exam-title').textContent = data.exam_title;
            document.getElementById('modal-student-name').textContent = data.student_name;
            document.getElementById('modal-status').textContent = data.status;
            document.getElementById('modal-attempt-at').textContent = data.attempt_at;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>
            