<?php
include '../link.php';
include '../db.php';
include 'admin_session.php';
// print_r($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="ri-logout-box-line fw-bold"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-lg-none p-2 bg-dark sidebar-toggler">
        <i class="ri-menu-line"></i>
    </div>

    <!-- sidebar -->
    <aside class="sidebar">
        <div class="header">
            <!-- <img src="../student dashboard/ksv.png" alt=""> -->
            <h1>EXAM EASE</h1>
        </div>
        <nav>
            <!-- <h2>Dashboard</h2> -->
            <a>
                <i class="ri-dashboard-line"></i>
                <p class="fw-bold">Manage Course</p>
                <a class="edit" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="ri-add-circle-fill"></i>Add Course</a>
                <a href="managecourse.php" class="edit"><i class="ri-add-circle-fill"></i>Manage Course</a>
            </a>
            <a>
                <i class="ri-book-line"></i>
                <p class="fw-bold">Manage Subject</p>
                <a class="edit" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="ri-add-circle-fill"></i>Add Subject</a>
                <a href="managesubject.php" class="edit"><i class="ri-add-circle-fill"></i>Manage Subjects</a>
            </a>
            <a>
                <i class="ri-mac-fill"></i>
                <p class="fw-bold">Manage Exam</p>
                <a class="edit" data-bs-toggle="modal" data-bs-target="#addExamModal">
                    <i class="ri-add-circle-fill"></i>Add Exam</a>
                <a href="manageexam.php" class="edit"><i class="ri-add-circle-fill"></i>Manage Exam</a>
            </a>
            <a>
                <i class="ri-user-line"></i>
                <p class="fw-bold">Manage Student</p>
                <a class="edit" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="ri-add-circle-fill"></i>Add Student
                </a>
                <a href="managestudent.php" class="edit"><i class="ri-add-circle-fill"></i>Manage Student</a>
            </a>
            <a>
                <i class="ri-user-add-line"></i>
                <p class="fw-bold">Manage Faculty</p>
                <a class="edit" data-bs-toggle="modal" data-bs-target="#addFacultyModal">
                    <i class="ri-add-circle-fill"></i>Add Faculty
                </a>
                <a href="managefaculty.php" class="edit"><i class="ri-add-circle-fill"></i>Manage Faculty</a>
            </a>
            <a>
                <i class="ri-trophy-line"></i>
                <p class="fw-bold">Reports</p>
                <a href="studentResult.php" class="edit"><i class="ri-add-circle-fill"></i>Student Results</a>
                <a href="studentRecords.php" class="edit"><i class="ri-add-circle-fill"></i>Student Records</a>

            </a>
        </nav>
    </aside>


    <!-- analystic dashboard -->
    <?php
     $totalCoursesQuery = "SELECT COUNT(course_id) AS total_courses FROM course";
     $totalSubjectsQuery = "SELECT COUNT(subject_id) AS total_subjects FROM subject";
     $totalStudentsQuery = "SELECT COUNT(student_id) AS total_students FROM student";
     $totalCompletedExamsQuery = "SELECT COUNT(exam_id) AS total_completed_exams FROM exam WHERE exam_status = 'Completed'";
     $query = "SELECT COUNT(*) AS total_faculties FROM faculty";
     $query = "SELECT COUNT(*) AS total_faculties FROM faculty";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $totalFaculties = $row['total_faculties'];
     
     $totalCourses = $conn->query($totalCoursesQuery)->fetch_assoc()['total_courses'];
     $totalSubjects = $conn->query($totalSubjectsQuery)->fetch_assoc()['total_subjects'];
     $totalStudents = $conn->query($totalStudentsQuery)->fetch_assoc()['total_students'];
     $totalCompletedExams = $conn->query($totalCompletedExamsQuery)->fetch_assoc()['total_completed_exams'];
     ?>
    <div class="container mt-5 pt-5">
        <h3 class="fw-bold fs-2">Analystic Dashboard</h3><br><br>
        <div class="row g-4">
            <div class="col-md-4 cards">
                <div class="card shadow-sm text-center" onclick="window.location.href='course_list.php';"
                    style="cursor: pointer;">
                    <div class="card-body card1">
                        <h5 class="card-title">Total Courses</h5>
                        <h3 class="display-4" id="totalCourses"><?php echo $totalCourses; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 cards">
                <div class="card shadow-sm text-center" onclick="window.location.href='subject_list.php';"
                    style="cursor: pointer;">
                    <div class="card-body card3">
                        <h5 class="card-title">Total Subjects</h5>
                        <h3 class="display-4" id="totalSubjects"><?php echo $totalSubjects; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 cards">
                <div class="card shadow-sm text-center" onclick="window.location.href='student_list.php';"
                    style="cursor: pointer;">
                    <div class="card-body card2">
                        <h5 class="card-title">Total Students</h5>
                        <h3 class="display-4" id="totalStudents"><?php echo $totalStudents; ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 cards">
                <div class="card shadow-sm text-center" onclick="window.location.href='completedexam_list.php';"
                    style="cursor: pointer;">
                    <div class="card-body card3">
                        <h5 class="card-title">Total Exams</h5>
                        <h3 class="display-4" id="totalExams"><?php echo $totalCompletedExams; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 cards">
                <div class="card shadow-sm text-center" onclick="window.location.href='showFaculty_list.php';"
                    style="cursor: pointer;">
                    <div class="card-body card1">
                        <h5 class="card-title">Total Faculties</h5>
                        <h3 class="display-4" id="totalFaculties"><?php echo $totalFaculties; ?></h3>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="addcourse.php">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" name="course_name" class="form-control" id="course_name"
                                placeholder="Enter course name" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addCourseModal">Save Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubjectForm" method="post" action="addsubject.php">
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="" disabled selected>Select a Course</option>
                                <?php
                                $courseQuery = "SELECT course_id, course_name FROM course";
                                $result = $conn->query($courseQuery);
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['course_id'] . '">' . $row['course_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Semester</label>
                            <select name="student_sem_id" class="form-select" required>
                                <option value="" disabled selected>Select a Semester</option>
                                <?php
                                include '../db.php';
                                $query = "SELECT student_sem_id, sem_name FROM student_sem";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['student_sem_id'] . '">' . $row['sem_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" id="subject_name"
                                placeholder="Enter subject name" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addSubjectModal">Add Subject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Exam Modal -->
    <div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExamModalLabel">Add Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="addexam.php">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Select Course</label>
                            <select class="form-select" name="course_id" id="course_id" required>
                                <option value="" disabled selected>Select a Course</option>
                                <?php
                            include '../db.php';
                            $courses = $conn->query("SELECT course_id, course_name FROM course");
                            while ($row = $courses->fetch_assoc()) {
                                echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="student_sem_id" class="form-label">Select Semester</label>
                            <select class="form-select" name="student_sem_id" id="student_sem_id" required>
                                <option value="" disabled selected>Select a Semester</option>
                                <?php
                            $semesters = $conn->query("SELECT student_sem_id, sem_name FROM student_sem");
                            while ($row = $semesters->fetch_assoc()) {
                                echo "<option value='{$row['student_sem_id']}'>{$row['sem_name']}</option>";
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Select Subject</label>
                            <select class="form-select" name="subject_id" id="subject_id" required>
                                <option value="" disabled selected>Select a Subject</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="exam_title" class="form-label">Exam Title</label>
                            <input type="text" class="form-control" id="exam_title" name="exam_title" required>
                        </div>

                        <div class="mb-3">
                            <label for="question_limit" class="form-label">Question Limit</label>
                            <input type="number" class="form-control" id="question_limit" name="question_limit"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="exam_start_time" class="form-label">Exam Start Time</label>
                            <input type="datetime-local" class="form-control" id="exam_start_time"
                                name="exam_start_time" required>
                        </div>

                        <div class="mb-3">
                            <label for="exam_end_time" class="form-label">Exam End Time</label>
                            <input type="datetime-local" class="form-control" id="exam_end_time" name="exam_end_time"
                                required>
                        </div>


                        <div class="mb-3">
                            <label for="exam_description" class="form-label">Exam Description</label>
                            <textarea class="form-control" id="exam_description" name="exam_description"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Save Exam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('course_id').addEventListener('change', updateSubjects);
    document.getElementById('student_sem_id').addEventListener('change', updateSubjects);

    function updateSubjects() {
        const courseId = document.getElementById('course_id').value;
        const semesterId = document.getElementById('student_sem_id').value;

        if (courseId && semesterId) {
            fetch(`addexam.php?course_id=${courseId}&student_sem_id=${semesterId}`)
                .then(response => response.json())
                .then(data => {
                    const subjectDropdown = document.getElementById('subject_id');
                    subjectDropdown.innerHTML = '<option value="" disabled selected>Select a Subject</option>';
                    data.forEach(subject => {
                        subjectDropdown.innerHTML +=
                            `<option value="${subject.subject_id}">${subject.subject_name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error fetching subjects:', error);
                    alert('There was an error fetching the subjects.');
                });
        }
    }
    </script>





    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="addstudent.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="studentSemester" class="form-label">Student Semester</label>
                            <select class="form-select" id="studentSemester" name="student_sem_id" required>
                                <option value="">Select Semester</option>
                                <?php
                            $semesterQuery = "SELECT student_sem_id, sem_name FROM student_sem";
                            $semesterResult = $conn->query($semesterQuery);
                            while ($row = $semesterResult->fetch_assoc()) {
                                echo '<option value="' . $row['student_sem_id'] . '">' . $row['sem_name'] . '</option>';
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="courseName" class="form-label">Course Name</label>
                            <select class="form-select" id="courseName" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php
                            $courseQuery = "SELECT course_id, course_name FROM course";
                            $result = $conn->query($courseQuery);
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['course_id'] . '">' . $row['course_name'] . '</option>';
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="studentName" class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="studentName" name="student_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="student_gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Birthdate</label>
                            <input type="date" name="student_birthdate" class="form-control" min="2000-01-01"
                                max="2006-12-31" value="2006-01-01" required>
                        </div>

                        <div class="mb-3">
                            <label for="studentEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="studentEmail" name="student_email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="student_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addFacultyModal" tabindex="-1" aria-labelledby="addFacultyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFacultyModalLabel">Add Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="addfaculty.php">
                    <div class="modal-body">
                        <!-- Faculty Name -->
                        <div class="mb-3">
                            <label for="facultyName" class="form-label">Faculty Name</label>
                            <input type="text" class="form-control" id="facultyName" name="faculty_name" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="facultyEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="facultyEmail" name="email" required>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phone_number" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="facultyPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="facultyPassword" name="password" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Add Faculty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




</body>

</html>