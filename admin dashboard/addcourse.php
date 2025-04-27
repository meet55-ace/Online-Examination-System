<?php
include '../link.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $course_name = $conn->real_escape_string(trim($_POST['course_name']));

        $sql_check = "SELECT * FROM course WHERE course_name = '$course_name'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Entry',
                        text: 'Course already exists!'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                });
            </script>";
        } else {
            $sql_insert = "INSERT INTO course (course_name) VALUES ('$course_name')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Course added successfully!'
                        }).then(() => {
                            window.location.href = 'dashboard.php';
                        });
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Database Error',
                            text: 'Error: {$conn->error}'
                        }).then(() => {
                            window.location.href = 'dashboard.php';
                        });
                    });
                </script>";
            }
        }
}
$conn->close();
?>
 