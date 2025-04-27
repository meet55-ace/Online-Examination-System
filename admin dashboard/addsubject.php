<?php
include '../link.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_sem_id = trim($_POST['student_sem_id']);
    $subject_name = trim($_POST['subject_name']);
    $course_id = trim($_POST['course_id']);

    

    $stmt_check = $conn->prepare("SELECT * FROM subject WHERE subject_name = ? AND student_sem_id = ? AND course_id = ?");
    $stmt_check->bind_param("sii", $subject_name, $student_sem_id, $course_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Entry',
                        text: 'Subject with the same name already exists for the selected semester and course!'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                });
            </script>";
    } else {
        $stmt_name_check = $conn->prepare("SELECT * FROM subject WHERE subject_name = ?");
        $stmt_name_check->bind_param("s", $subject_name);
        $stmt_name_check->execute();
        $result_name_check = $stmt_name_check->get_result();

        if ($result_name_check->num_rows > 0) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Subject Name',
                            text: 'A subject with the same name already exists!'
                        }).then(() => {
                            window.location.href = 'dashboard.php';
                        });
                    });
                </script>";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO subject (student_sem_id, subject_name, course_id) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("isi", $student_sem_id, $subject_name, $course_id);

            if ($stmt_insert->execute()) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Subject added successfully!'
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
                                title: 'Failed to Add Subject',
                                text: 'Error: {$conn->error}'
                            }).then(() => {
                                window.location.href = 'dashboard.php';
                            });
                        });
                    </script>";
            }

            $stmt_insert->close();
        }

        $stmt_name_check->close();
    }

    $stmt_check->close();
    $conn->close();
}
