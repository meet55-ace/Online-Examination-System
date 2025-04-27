<?php
include '../link.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);  
    $subject_name = trim($_POST['subject_name']);
    $semester_name = trim($_POST['sem_name']);
    $course_name = trim($_POST['course_name']);

    if (!empty($subject_name)) {
        $checkQuery = $conn->prepare("SELECT COUNT(*) FROM subject WHERE subject_name = ? AND subject_id != ?");
        $checkQuery->bind_param("si", $subject_name, $subject_id);
        $checkQuery->execute();
        $checkQuery->bind_result($count);
        $checkQuery->fetch();
        $checkQuery->close();

        if ($count > 0) {
            echo "<script>alert('Subject name already exists.');</script>";
        } else {
            $stmt = $conn->prepare(
                "UPDATE subject 
                 SET subject_name = ?
                 WHERE subject_id = ?"
            );
            $stmt->bind_param("si", $subject_name,  $subject_id);

            if ($stmt->execute()) {
                echo "<script>alert('Subject successfully updated');</script>";
            } else {
                echo "<script>alert('Error occurred during update');</script>";
            }
            $stmt->close();
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h3 class="fw-bold mb-4">Manage Subjects</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="subjectTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Subject Name</th>
                        <th>Semester</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT s.subject_id, s.subject_name, ss.sem_name, c.course_name 
                              FROM subject s 
                              JOIN student_sem ss ON s.student_sem_id = ss.student_sem_id 
                              JOIN course c ON s.course_id = c.course_id";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-" . $row['subject_id'] . "'>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sem_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-warning btn-sm update-btn' data-id='" . $row['subject_id'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' width='16' height='16'>
                                <path d='M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z'/>
                            </svg> Edit</button> ";
                    
                    echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['subject_id'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' width='16' height='16'>
                                <path fill='white' d='M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z'/>
                            </svg> Delete</button>";
                    
        echo "</td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No subjects found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateSubjectModal" tabindex="-1" aria-labelledby="updateSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateSubjectModalLabel">Update Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateSubjectForm">
                        <div class="mb-3">
                            <label for="subjectName" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subjectName" name="subject_name" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="sem_name" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="sem_name" name="sem_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_name" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div> -->
                        <input type="hidden" id="subjectId" name="subject_id">
                        <button type="submit" class="btn btn-primary">Update Subject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>

    <script>
        // update
        $(document).on('click', '.update-btn', function() {
            var row = $(this).closest('tr');
            var subjectId = $(this).data('id');
            var subjectName = row.find('td:nth-child(2)').text().trim();
            var semester = row.find('td:nth-child(3)').text().trim();
            var course = row.find('td:nth-child(4)').text().trim();

            $('#subjectId').val(subjectId);
            $('#subjectName').val(subjectName);
            $('#sem_name').val(semester);
            $('#course_name').val(course);

            $('#updateSubjectModal').modal('show');
        });

        $('#updateSubjectForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: 'managesubject.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    try {
                        var jsonResponse = JSON.parse(response);

                        if (jsonResponse.status === 'success') {
                            var subjectId = $('#subjectId').val();
                            var subjectName = $('#subjectName').val();
                            var semester = $('#sem_name').val();
                            var course = $('#course_name').val();

                            $('#row-' + subjectId + ' td:nth-child(2)').text(subjectName);
                            $('#row-' + subjectId + ' td:nth-child(3)').text(semester);
                            $('#row-' + subjectId + ' td:nth-child(4)').text(course);

                            $('#updateSubjectModal').modal('hide');
                            Swal.fire('Updated!', 'Subject has been updated successfully.', 'success');
                        } else {
                            Swal.fire('Error!', jsonResponse.message || 'Failed to update the subject.', 'error');
                        }
                    } catch (e) {
                        Swal.fire('Updated!', 'Subject has been updated successfully.', 'success')
                        .then(() => {
                                        window.location.href = "managesubject.php"; 
                                    });
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                }
            });
        });

        // delete
        $(document).on('click', '.delete-btn', function() {
            const subjectId = $(this).data('id'); 
            const row = $(this).closest('tr'); 

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this subject?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'deletesubject.php', 
                        type: 'POST',
                        data: { subject_id: subjectId },
                        success: function(response) {
                            if (response.trim() === 'success') {
                                row.remove(); 
                                Swal.fire('Deleted!', 'The subject has been successfully deleted.', 'success');
                            } else {
                                Swal.fire('Error!', 'Unable to delete the subject.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', 'An unexpected error occurred: ' + error, 'error');
                        }
                    });
                } else {
                    Swal.fire('Cancelled', 'The subject was not deleted.', 'info');
                }
            });
        });
    </script>
</body>

</html>
