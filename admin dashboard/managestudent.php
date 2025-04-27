<?php
include '../db.php';
include '../link.php';

$query = "
    SELECT 
        student.student_id, 
        student.student_name, 
        student.student_gender, 
        student.student_birthdate, 
        student.student_status, 
        student.student_email, 
        course.course_name, 
        student_sem.sem_name 
    FROM student
    LEFT JOIN course ON student.course_id = course.course_id
    LEFT JOIN student_sem ON student.student_sem_id = student_sem.student_sem_id;  
";

$result = $conn->query($query);

$students = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Manage Students</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr id="row-<?= htmlspecialchars($student['student_id']) ?>">
                            <td><?= htmlspecialchars($student['student_name']) ?></td>
                            <td><?= htmlspecialchars($student['student_gender']) ?></td>
                            <td><?= htmlspecialchars($student['student_birthdate']) ?></td>
                            <td><?= htmlspecialchars($student['course_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($student['sem_name'] ?? 'N/A') ?></td> <!-- Display semester name -->
                            <td><?= htmlspecialchars($student['student_email']) ?></td>
                            <td><?= $student['student_status'] === 'active' ? 'Active' : 'Inactive' ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm update-btn"
                                    data-id="<?= htmlspecialchars($student['student_id']) ?>"
                                    data-student_name="<?= htmlspecialchars($student['student_name']) ?>"
                                    data-student_gender="<?= htmlspecialchars($student['student_gender']) ?>"
                                    data-student_birthdate="<?= htmlspecialchars($student['student_birthdate']) ?>"
                                    data-student_status="<?= htmlspecialchars($student['student_status']) ?>"
                                    data-student_sem="<?= htmlspecialchars($student['sem_name']) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" fill="currentColor" style="margin-right: 5px;">
                                        <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                    </svg>
                                    Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).on('click', '.update-btn', function() {
            const studentId = $(this).data('id');
            const studentName = $(this).data('student_name');
            const studentGender = $(this).data('student_gender');
            const studentBirthdate = $(this).data('student_birthdate');
            const studentStatus = $(this).data('student_status');
            const studentSem = $(this).data('student_sem');

            Swal.fire({
                title: 'Update Student',
                html: ` 
                    <input id="swal-input1" class="swal2-input" placeholder="Name" value="${studentName}">
                    <select id="swal-input2" class="swal2-input">
                        <option value="Male" ${studentGender === 'Male' ? 'selected' : ''}>Male</option>
                        <option value="Female" ${studentGender === 'Female' ? 'selected' : ''}>Female</option>
                        <option value="Other" ${studentGender === 'Other' ? 'selected' : ''}>Other</option>
                    </select>
                    <input id="swal-input3" class="swal2-input" type="date" value="${studentBirthdate}">
                    <select id="swal-input4" class="swal2-input">
                        <option value="active" ${studentStatus === 'active' ? 'selected' : ''}>Active</option>
                        <option value="inactive" ${studentStatus === 'inactive' ? 'selected' : ''}>Inactive</option>
                    </select>
                    <input id="swal-input5" class="swal2-input" value="${studentSem}">
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return {
                        id: studentId,
                        name: document.getElementById('swal-input1').value,
                        gender: document.getElementById('swal-input2').value,
                        birthdate: document.getElementById('swal-input3').value,
                        status: document.getElementById('swal-input4').value,
                        sem: document.getElementById('swal-input5').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const updatedData = result.value;

                    $.ajax({
                        url: 'update_student.php',
                        type: 'POST',
                        data: updatedData,
                        success: function(response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire('Success!', 'Student updated successfully.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error!', res.message || 'Failed to update student.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        }
                    });
                }
            });
        });
    </script>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>

</body>

</html>
