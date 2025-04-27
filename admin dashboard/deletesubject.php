<?php
include '../link.php';  
include '../db.php';    

header('Content-Type: text/plain');  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $subjectId = intval($_POST['subject_id']); 

    $query = "DELETE FROM subject WHERE subject_id = ?";
    
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $subjectId);

        if ($stmt->execute()) {
            echo "success";  
        } else {
            echo "Error deleting subject."; 
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
