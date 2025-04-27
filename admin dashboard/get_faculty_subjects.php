<?php
include '../db.php';

if (isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'];

    $query = "SELECT sb.subject_name, c.course_name, s.sem_name
              FROM faculty_subject fs
              JOIN subject sb ON fs.subject_id = sb.subject_id
              JOIN course c ON sb.course_id = c.course_id
              JOIN student_sem s ON sb.student_sem_id = s.student_sem_id
              WHERE fs.faculty_id = $faculty_id";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Course</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>";
        while ($subject = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$subject['subject_name']}</td>
                    <td>{$subject['course_name']}</td>
                    <td>{$subject['sem_name']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='text-muted'>No subjects assigned.</p>";
    }
}
?>
