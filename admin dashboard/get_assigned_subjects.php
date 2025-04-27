<?php

include '../db.php';

$faculty_id = $_POST['faculty_id'];

$assignedQuery = "
    SELECT s.subject_id, s.subject_name, fs.faculty_subject_id 
    FROM faculty_subject fs
    JOIN subject s ON fs.subject_id = s.subject_id
    WHERE fs.faculty_id = ?
";
$assignedStmt = $conn->prepare($assignedQuery);
$assignedStmt->bind_param("i", $faculty_id);
$assignedStmt->execute();
$assignedResult = $assignedStmt->get_result();

$assignedSubjects = [];
while ($row = $assignedResult->fetch_assoc()) {
    $assignedSubjects[] = $row;
}

$assignedIds = array_column($assignedSubjects, 'subject_id');

$availableQuery = "
    SELECT s.subject_id, s.subject_name
    FROM subject s
    WHERE s.subject_id NOT IN (
        SELECT DISTINCT subject_id FROM faculty_subject
    )
";
$availableResult = $conn->query($availableQuery);

$availableSubjects = [];
while ($row = $availableResult->fetch_assoc()) {
    $availableSubjects[] = $row;
}

echo json_encode([
    "assigned" => $assignedSubjects,
    "available" => $availableSubjects
]);
?>
