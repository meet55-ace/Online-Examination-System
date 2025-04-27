<?php
include '../link.php';  
include '../db.php';    

header('Content-Type: text/plain');  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $courseId = intval($_POST['course_id']); 

    $query = "DELETE FROM course WHERE course_id = ?";
    
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $courseId);

        if ($stmt->execute()) {
            echo "success";  
        } else {
            echo "Error deleting course.";  
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
