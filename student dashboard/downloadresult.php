<?php
require('tcpdf/tcpdf.php'); 
include 'student_session.php'; 
include '../db.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['student_id'])) {
    die("Error: You must log in to view this page.");
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$query = "SELECT s.student_name, s.student_birthdate, c.course_name, sem.sem_name 
          FROM student s
          JOIN course c ON s.course_id = c.course_id
          JOIN student_sem sem ON s.student_sem_id = sem.student_sem_id
          WHERE s.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Error: No student found with the provided ID.");
}

// Exam Results
$result_sql = "SELECT sub.subject_name, se.exam_marks_obtained, se.exam_max_marks
               FROM student_exam_results se
               JOIN exam e ON se.exam_id = e.exam_id
               JOIN subject sub ON e.subject_id = sub.subject_id
               WHERE se.student_id = ?";
$stmt = $conn->prepare($result_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$exam_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Semester Result
$sem_result_sql = "SELECT max_marks, marks_obtained, percentage, status
                   FROM student_results
                   WHERE student_id = ?";
$stmt = $conn->prepare($sem_result_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$sem_result = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

$percentage = isset($sem_result['percentage']) ? (float) $sem_result['percentage'] : 0.0;
$max_marks = isset($sem_result['max_marks']) ? (float) $sem_result['max_marks'] : 0.0;
$marks_obtained = isset($sem_result['marks_obtained']) ? (float) $sem_result['marks_obtained'] : 0.0;
$status = isset($sem_result['status']) ? $sem_result['status'] : 'Unknown';


$student_name = isset($student['student_name']) ? $student['student_name'] : 'Student Result';

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle($student_name);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10.0, 20.0, 10.0);
$pdf->SetAutoPageBreak(true, 15.0);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Exam Ease', 0, 1, 'C');

// Student Info
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(5);
$pdf->Cell(0, 10, "Student ID: " . $student_id, 0, 1);
$pdf->Cell(0, 10, "Student Name: " . $student['student_name'], 0, 1);
$pdf->Cell(0, 10, "Date of Birth: " . $student['student_birthdate'], 0, 1);
$pdf->Cell(0, 10, "Course: " . $student['course_name'], 0, 1);
$pdf->Cell(0, 10, "Semester: " . $student['sem_name'], 0, 1);

$pdf->Ln(5);
if (empty($exam_results)) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Result Awaited', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Your results have not been published yet. Please check back later.', 0, 1, 'C');
} else {
    // Table Header
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(80, 10, 'Subject Name', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Max Marks', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Marks Obtained', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 12);
    $total_max_marks = 0.0;
    $total_marks_obtained = 0.0;
    
    foreach ($exam_results as $row) {
        $exam_max_marks = isset($row['exam_max_marks']) ? (float) $row['exam_max_marks'] : 0.0;
        $exam_marks_obtained = isset($row['exam_marks_obtained']) ? (float) $row['exam_marks_obtained'] : 0.0;

        $pdf->Cell(80, 10, $row['subject_name'], 1, 0, 'C');
        $pdf->Cell(40, 10, $exam_max_marks, 1, 0, 'C');
        $pdf->Cell(40, 10, $exam_marks_obtained, 1, 1, 'C');

        $total_max_marks += $exam_max_marks;
        $total_marks_obtained += $exam_marks_obtained;
    }

    // Total Marks
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(80, 10, 'Total', 1, 0, 'C');
    $pdf->Cell(40, 10, $total_max_marks, 1, 0, 'C');
    $pdf->Cell(40, 10, $total_marks_obtained, 1, 1, 'C');

    // Percentage & Status
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, "Percentage: " . number_format($percentage, 2) . "%", 0, 1);
    $pdf->Cell(0, 10, "Result: " . $status, 0, 1);
}

// Output PDF
$student_name = isset($student['student_name']) ? preg_replace('/[^A-Za-z0-9]/', '_', $student['student_name']) : 'Student_Result';
$pdf->Output($student_name . '_Result.pdf', 'D');


?>
