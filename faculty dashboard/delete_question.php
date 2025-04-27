<?php
include '../db.php';
header("Content-Type: text/plain"); // Ensure correct response type

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["question_id"])) {
    http_response_code(400); // Bad request
    echo "Invalid request";
    exit();
}

$id = intval($_POST["question_id"]); // Ensure it's an integer

$stmt = $conn->prepare("DELETE FROM question WHERE question_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}

$stmt->close();
?>
