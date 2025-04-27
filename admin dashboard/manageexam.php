<?php
include '../db.php';
include '../link.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3 class="fw-bold mb-4">Exam List</h3>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Exam Title</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Subject</th>
                    <th>Question Limit</th>
                    <th>Start Time</th>
                    <th>End Time</th> 
                    <!-- <th>Exam Marks</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "
                SELECT 
                    exam.exam_id,
                    exam.exam_title,
                    course.course_name,
                    student_sem.sem_name AS semester_name,
                    subject.subject_name,
                    exam.question_limit,
                    exam.exam_start_time,
                    exam.exam_end_time  
                FROM exam
                INNER JOIN course ON exam.course_id = course.course_id
                INNER JOIN student_sem ON exam.student_sem_id = student_sem.student_sem_id
                INNER JOIN subject ON exam.subject_id = subject.subject_id
                WHERE exam.exam_end_time > NOW()  
            ";
            
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $index = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "
                        <tr id='row-{$row['exam_id']}'>
                            <td>{$index}</td>
                            <td>{$row['exam_title']}</td>
                            <td>{$row['course_name']}</td>
                            <td>{$row['semester_name']}</td>
                            <td>{$row['subject_name']}</td>
                            <td>{$row['question_limit']}</td>
                            <td>{$row['exam_start_time']}</td>
                            <td>{$row['exam_end_time']}</td> <!-- End Time Added -->
                            <td>
                                <a href='editExam.php?exam_id={$row['exam_id']}' class='btn btn-primary btn-sm edit-btn'>Edit</a>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['exam_id']}'>Delete</button>
                            </td>
                        </tr>";
                        $index++;
                    }
                } else {
                    echo '<tr><td colspan="10" class="text-center">No Exams Found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>

    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function() {
                const examId = $(this).data('id');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to delete this exam?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteexam.php',
                            type: 'POST',
                            data: { exam_id: examId },
                            success: function(response) {
                                if (response.trim() === 'success') {
                                    row.remove();
                                    Swal.fire('Deleted!', 'The exam has been successfully deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', 'Unable to delete the exam: ' + response, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('Error!', 'An unexpected error occurred: ' + error, 'error');
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire('Cancelled', 'The exam was not deleted.', 'info');
                    }
                });
            });
        });
    </script>
</body>

</html>
