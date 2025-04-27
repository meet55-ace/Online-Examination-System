<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $course_name = trim($_POST['course_name']);

    if (!empty($course_name)) {
        $checkQuery = $conn->prepare("SELECT COUNT(*) FROM course WHERE course_name = ? AND course_id != ?");
        $checkQuery->bind_param("si", $course_name, $course_id);
        $checkQuery->execute();
        $checkQuery->bind_result($count);
        $checkQuery->fetch();
        $checkQuery->close();

        if ($count > 0) {
            echo 'exists';
        } else {
            $stmt = $conn->prepare("UPDATE course SET course_name = ? WHERE course_id = ?");
            $stmt->bind_param("si", $course_name, $course_id);

            if ($stmt->execute()) {
                echo 'success';
            } else {
                echo 'error';
            }

            $stmt->close();
        }
    } else {
        echo 'empty';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <?php include '../link.php'; ?>
</head>

<body>
    <div class="container mt-5">
        <h3 class="fw-bold mb-4">Course List</h3>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Course Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="courseTableBody">
                <?php
                $sql = "SELECT * FROM course";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr id='row-" . $row['course_id'] . "'>";
                        echo "<td>" . htmlspecialchars($row['course_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-warning btn-sm me-1 update-btn' data-id='" . $row['course_id'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' width='16' height='16' fill='currentColor'>
                                <path d='M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z'/>
                            </svg> Edit
                        </button>";
                        echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['course_id'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' width='16' height='16' fill='currentColor'>
                                <path d='M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z'/>
                            </svg> Delete
                        </button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No courses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Update Course Modal -->
    <div class="modal fade" id="updateCourseModal" tabindex="-1" aria-labelledby="updateCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateCourseModalLabel">Update Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateCourseForm" method="post">
                        <div class="mb-3">
                            <label for="courseName" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="courseName" name="course_name" required>
                            <input type="hidden" id="courseId" name="course_id">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Update Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>

    <script>
        // Update Course
        $('.update-btn').click(function() {
            var courseId = $(this).data('id');
            var row = $(this).closest('tr');
            var courseName = row.find('td:nth-child(2)').text().trim();

            $('#courseId').val(courseId);
            $('#courseName').val(courseName);
            $('#updateCourseModal').modal('show');
        });

        $('#updateCourseForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: 'managecourse.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    const status = response.trim();

                    if (status === 'success') {
                        var courseId = $('#courseId').val();
                        var courseName = $('#courseName').val();
                        $(`#row-${courseId} td:nth-child(2)`).text(courseName);

                        $('#updateCourseModal').modal('hide');
                        Swal.fire('Updated!', 'Course has been updated successfully.', 'success')

                            .then(() => location.reload());
                    } else if (status === 'exists') {
                        Swal.fire('Error!', 'Course name already exists. Please choose a different name.', 'error')
                            .then(() => location.reload());
                    } else if (status === 'empty') {
                        Swal.fire('Warning!', 'Course name cannot be empty.', 'warning')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error!', 'Failed to update the course.', 'error')
                            .then(() => location.reload());
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                }
            });
        });

        //  delete
        $(document).ready(function() {
            $('.delete-btn').click(function() {
                const courseId = $(this).data('id');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to delete this course?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deletecourse.php',
                            type: 'POST',
                            data: {
                                course_id: courseId
                            },
                            success: function(response) {
                                if (response.trim() === 'success') {
                                    row.remove();
                                    Swal.fire(
                                        'Error!',
                                        'Unable to delete the course: ' + response,
                                        'error'
                                    ).then(() => {
                                        window.location.href = "managecourse.php";
                                    });
                                } else {
                                    Swal.fire(
                                        'Deleted!',
                                        'The course has been successfully deleted.',
                                        'success'
                                    ).then(() => {
                                        window.location.href = "managecourse.php"; // Redirect after deletion
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'An unexpected error occurred: ' + error,
                                    'error'
                                );
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'The course was not deleted.',
                            'info'
                        );
                    }
                });
            });
        });
    </script>
</body>

</html>