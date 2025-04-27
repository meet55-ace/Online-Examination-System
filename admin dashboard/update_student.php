<?php
include '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['id'] ?? null;
    $studentName = $_POST['name'] ?? null;
    $studentGender = $_POST['gender'] ?? null;
    $studentBirthdate = $_POST['birthdate'] ?? null;
    $studentStatus = $_POST['status'] ?? null;

    if (!$studentId || !$studentName || !$studentGender || !$studentBirthdate || !$studentStatus) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $query = "
        UPDATE student
        SET student_name = ?, 
            student_gender = ?, 
            student_birthdate = ?, 
            student_status = ?
        WHERE student_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $studentName, $studentGender, $studentBirthdate, $studentStatus, $studentId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update student.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
