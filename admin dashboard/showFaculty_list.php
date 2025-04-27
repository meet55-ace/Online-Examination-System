<?php

include '../link.php';
include '../db.php';

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$semester_id = isset($_GET['semester_id']) ? $_GET['semester_id'] : '';

// Fetch all courses
$course_query = "SELECT * FROM course";
$course_result = mysqli_query($conn, $course_query);

// Fetch all semesters
$semester_query = "SELECT * FROM student_sem";
$semester_result = mysqli_query($conn, $semester_query);

// Fetch faculties with optional filters
$query = "SELECT DISTINCT f.faculty_id, f.faculty_name, f.email, f.phone_number
          FROM faculty f
          LEFT JOIN faculty_subject fs ON f.faculty_id = fs.faculty_id
          LEFT JOIN subject sb ON fs.subject_id = sb.subject_id
          LEFT JOIN course c ON sb.course_id = c.course_id
          LEFT JOIN student_sem s ON sb.student_sem_id = s.student_sem_id
          WHERE 1";

if (!empty($course_id)) {
    $query .= " AND c.course_id = '$course_id'";
}
if (!empty($semester_id)) {
    $query .= " AND s.student_sem_id = '$semester_id'";
}

$result = mysqli_query($conn, $query);
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FacultyList</title>
</head>

<body>

    <body>
        <div class="container mt-4">
            <h2 class="mb-4">Faculty List</h2>

            <!-- Filter Form -->
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="course" class="form-label">Select Course:</label>
                    <select name="course_id" id="course" class="form-select">
                        <option value="">All Courses</option>
                        <?php while ($course = mysqli_fetch_assoc($course_result)) { ?>
                        <option value="<?= $course['course_id']; ?>"
                            <?= ($course_id == $course['course_id']) ? 'selected' : '' ?>>
                            <?= $course['course_name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="semester" class="form-label">Select Semester:</label>
                    <select name="semester_id" id="semester" class="form-select">
                        <option value="">All Semesters</option>
                        <?php while ($semester = mysqli_fetch_assoc($semester_result)) { ?>
                        <option value="<?= $semester['student_sem_id']; ?>"
                            <?= ($semester_id == $semester['student_sem_id']) ? 'selected' : '' ?>>
                            <?= $semester['sem_name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <!-- Faculty Table -->
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Faculty Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                    $index = 1;
                    while ($faculty = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $index++; ?></td>
                        <td><?= $faculty['faculty_name']; ?></td>
                        <td><?= $faculty['email']; ?></td>
                        <td><?= $faculty['phone_number']; ?></td>
                        <td>
                            <button class="btn btn-info btn-sm view-subjects"
                                data-faculty-id="<?= $faculty['faculty_id']; ?>">View Assigned Subjects</button>
                        </td>
                    </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No faculties found.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Assigned Subjects</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="subjectList"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fw-bold text-center fs-2 p-5">
            <<<...back to the <a href="dashboard.php">Dashboard</a>
        </div>

        <script>
        $(document).ready(function() {
            $(".view-subjects").click(function() {
                var facultyId = $(this).data("faculty-id");

                $.ajax({
                    url: "get_faculty_subjects.php",
                    type: "POST",
                    data: {
                        faculty_id: facultyId
                    },
                    success: function(response) {
                        $("#subjectList").html(response);
                        $("#subjectsModal").modal("show");
                    }
                });
            });
        });
        </script>


    </body>

</html>