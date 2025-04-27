<?php
include '../link.php';  
include '../db.php';    

header('Content-Type: text/plain');  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_id'])) {
    $examId = intval($_POST['exam_id']); 

    $query = "DELETE FROM exam WHERE exam_id = ?";
    
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $examId);

        if ($stmt->execute()) {
            echo "success";  
        } else {
            echo "Error deleting exam.";  
        }

        $stmt->close(); 
    } else {
        echo "Error preparing query.";  
    }

    $conn->close(); 
} else {
    echo "Invalid request.";  
}
?>
