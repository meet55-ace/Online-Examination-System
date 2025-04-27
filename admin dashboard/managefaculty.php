<?php
include '../link.php';
include '../db.php';
include 'admin_session.php';

$query = "
    SELECT f.faculty_id, f.faculty_name, f.email, 
           GROUP_CONCAT(s.subject_name ORDER BY s.subject_name SEPARATOR ', ') AS subjects
    FROM faculty f
    LEFT JOIN faculty_subject fs ON f.faculty_id = fs.faculty_id
    LEFT JOIN subject s ON fs.subject_id = s.subject_id
    GROUP BY f.faculty_id
    ORDER BY f.faculty_id;
";

$result = $conn->query($query);
?>

<div class="container mt-4">
    <h2>Manage Faculty</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Faculty Name</th>
                <th>Email</th>
                <th>Assigned Subjects</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count = 1;
            while ($faculty = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= $faculty['faculty_name'] ?></td>
                <td><?= $faculty['email'] ?></td>
                <td>
                    <?= !empty($faculty['subjects']) ? $faculty['subjects'] : '<span class="text-muted">No subjects assigned</span>' ?>
                </td>
                <td>
                    <button class="btn btn-primary assignSubjectsBtn" data-faculty-id="<?= $faculty['faculty_id'] ?>"
                        data-faculty-name="<?= $faculty['faculty_name'] ?>" data-bs-toggle="modal"
                        data-bs-target="#assignSubjectsModal">Manage Subjects</button>
                    <button class="btn btn-warning editFacultyBtn" data-faculty-id="<?= $faculty['faculty_id'] ?>"
                        data-faculty-name="<?= $faculty['faculty_name'] ?>" data-email="<?= $faculty['email'] ?>"
                        data-bs-toggle="modal" data-bs-target="#editFacultyModal">Edit</button>
                    <button class="btn btn-danger deleteFacultyBtn"
                        data-faculty-id="<?= $faculty['faculty_id'] ?>">Delete</button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Assign Subjects Modal -->
<div class="modal fade" id="assignSubjectsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Subjects</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignSubjectsForm">
                <div class="modal-body">
                    <input type="hidden" id="facultyId" name="faculty_id">
                    <p><strong>Faculty Name:</strong> <span id="facultyName"></span></p>

                    <label>Select Subjects</label>
                    <select id="subjects" name="subjects[]" class="form-select" multiple required></select>

                    <hr>
                    <h6>Assigned Subjects</h6>
                    <ul id="assignedSubjectsList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Faculty Modal -->
<div class="modal fade" id="editFacultyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Faculty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFacultyForm">
                <div class="modal-body">
                    <input type="hidden" id="editFacultyId" name="faculty_id">
                    <label>Name</label>
                    <input type="text" id="editFacultyName" name="faculty_name" class="form-control" required>
                    <label>Email</label>
                    <input type="email" id="editFacultyEmail" name="email" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="fw-bold text-center fs-2 p-5">
    <<<...back to the <a href="dashboard.php">Dashboard</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

$(".assignSubjectsBtn").click(function () {
    var facultyId = $(this).data("faculty-id");
    var facultyName = $(this).data("faculty-name");
    $("#facultyId").val(facultyId);
    $("#facultyName").text(facultyName);

    $.post("get_assigned_subjects.php", { faculty_id: facultyId }, function (response) {
        console.log("Assigned Subjects Response:", response); 

        $("#assignedSubjectsList").empty();
        $("#subjects").empty();

        if (!response || !response.assigned || !response.available) {
            Swal.fire({ icon: "error", title: "Error", text: "Invalid data received!" });
            return;
        }

        let assignedIds = response.assigned.map(subject => subject.subject_id);

        response.assigned.forEach(function (subject) {
            $("#assignedSubjectsList").append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${subject.subject_name}
                    <button class="btn btn-danger btn-sm deleteSubjectBtn" data-faculty-subject-id="${subject.faculty_subject_id}">Delete</button>
                </li>
            `);
        });

        response.available.forEach(function (subject) {
            if (!assignedIds.includes(subject.subject_id)) { 
                $("#subjects").append(`<option value="${subject.subject_id}">${subject.subject_name}</option>`);
            }
        });

    }, "json").fail(function () {
        Swal.fire({ icon: "error", title: "Error", text: "Failed to load subjects!" });
    });
});

$("#assignSubjectsForm").submit(function (event) {
    event.preventDefault();
    $.post("assign_subjects.php", $(this).serialize(), function (response) {
        console.log("Assign Response:", response); // Debugging

        Swal.fire(response === "success" ? {
            icon: "success",
            title: "Subjects Assigned!",
            timer: 2000,
            showConfirmButton: false
        } : {
            icon: "error",
            title: "Error",
            text: "Failed to assign subjects!"
        }).then(() => location.reload());

    }).fail(function () {
        Swal.fire({ icon: "error", title: "Error", text: "Server error, try again later!" });
    });
});

$(document).on("click", ".deleteSubjectBtn", function () {
    var facultySubjectId = $(this).data("faculty-subject-id");

    Swal.fire({
        title: "Are you sure?",
        text: "This subject will be removed!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("delete_assigned_subject.php", { faculty_subject_id: facultySubjectId }, function (response) {
                console.log("Delete Response:", response); // Debugging

                Swal.fire(response === "success" ? {
                    icon: "success",
                    title: "Deleted!",
                    timer: 2000,
                    showConfirmButton: false
                } : {
                    icon: "error",
                    title: "Error",
                    text: "Failed to delete subject!"
                }).then(() => location.reload());

            }).fail(function () {
                Swal.fire({ icon: "error", title: "Error", text: "Server error, try again later!" });
            });
        }
    });
});

    $(document).on("click", ".editFacultyBtn", function() {
        var facultyId = $(this).data("faculty-id");
        var facultyName = $(this).data("faculty-name");
        var facultyEmail = $(this).data("email");

        $("#editFacultyId").val(facultyId);
        $("#editFacultyName").val(facultyName);
        $("#editFacultyEmail").val(facultyEmail);
    });

    $("#editFacultyForm").submit(function(event) {
        event.preventDefault();
        $.post("edit_faculty.php", $(this).serialize(), function(response) {
            console.log("Edit Faculty Response:", response);

            Swal.fire(response === "success" ? {
                icon: "success",
                title: "Updated!",
                timer: 2000,
                showConfirmButton: false
            } : {
                icon: "error",
                title: "Error",
                text: "Failed to update faculty!"
            }).then(() => location.reload());

        }).fail(function() {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Server error, try again later!"
            });
        });
    });

    $(document).on("click", ".deleteFacultyBtn", function() {
        var facultyId = $(this).data("faculty-id");

        Swal.fire({
            title: "Are you sure?",
            text: "This faculty will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("delete_faculty.php", {
                    faculty_id: facultyId
                }, function(response) {
                    console.log("Delete Faculty Response:", response);

                    Swal.fire(response === "success" ? {
                        icon: "success",
                        title: "Deleted!",
                        timer: 2000,
                        showConfirmButton: false
                    } : {
                        icon: "error",
                        title: "Error",
                        text: "Failed to delete faculty!"
                    }).then(() => location.reload());

                }).fail(function() {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Server error, try again later!"
                    });
                });
            }
        });
    });

});
</script>