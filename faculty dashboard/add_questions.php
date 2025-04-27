    <?php
    include '../link.php';
    include '../db.php';
    include 'faculty_session.php';
    require '../vendor/autoload.php'; // Load PhpSpreadsheet

    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Get Exam ID & Subject ID from URL
    if (!isset($_GET['exam_id']) || !isset($_GET['subject_id'])) {
        echo "<script>alert('Invalid access!'); window.location.href='faculty_dashboard.php';</script>";
        exit;
    }

    $exam_id = $_GET['exam_id'];
    $subject_id = $_GET['subject_id'];

   // Fetch exam details
$exam_query = "SELECT exam_title, question_limit FROM exam WHERE exam_id = $exam_id";
$exam_result = mysqli_query($conn, $exam_query);
$exam_data = mysqli_fetch_assoc($exam_result);

$exam_title = $exam_data['exam_title']; // Get exam title
$question_limit = $exam_data['question_limit'];


    // Get current question count
    $question_count_query = "SELECT COUNT(*) as total FROM question WHERE exam_id = $exam_id AND subject_id = $subject_id";
    $question_count_result = mysqli_query($conn, $question_count_query);
    $question_count = mysqli_fetch_assoc($question_count_result)['total'];

    // Handle file upload
    if (isset($_POST['upload'])) {
        if ($question_count >= $question_limit) {
            echo "<script>alert('Question limit reached! Cannot add more questions.');</script>";
        } else {
            $file = $_FILES['excel_file']['tmp_name'];

            if ($file) {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // Skip the first row (headers)
                array_shift($data);

                foreach ($data as $row) {
                    if ($question_count >= $question_limit) {
                        echo "<script>alert('Question limit reached! Some questions were not added.');</script>";
                        break;
                    }

                    $question = $row[0];
                    $op1 = $row[1];
                    $op2 = $row[2];
                    $op3 = $row[3];
                    $op4 = $row[4];
                    $correct_option = $row[5];
                    $ques_mark = $row[6];

                    // Insert into database
                    $query = "INSERT INTO question (exam_id, subject_id, question, op1, op2, op3, op4, correct_option, ques_mark) 
                            VALUES ('$exam_id', '$subject_id', '$question', '$op1', '$op2', '$op3', '$op4', '$correct_option', '$ques_mark')";
                    mysqli_query($conn, $query);
                    $question_count++; // Increment count
                }

                echo "<script>alert('Questions uploaded successfully!'); window.location.href='add_questions.php?exam_id=$exam_id&subject_id=$subject_id';</script>";
            } else {
                echo "<script>alert('Please upload a valid Excel file.');</script>";
            }
        }
    }

    // Handle manual question addition
    // Handle manual question addition or update
if (isset($_POST['add_question'])) {
    $question_id = $_POST['question_id'] ?? null;
    $question = $_POST['question'];
    $op1 = $_POST['op1'];
    $op2 = $_POST['op2'];
    $op3 = $_POST['op3'];
    $op4 = $_POST['op4'];
    $correct_option = $_POST['correct_option'];
    $ques_mark = $_POST['ques_mark'];

    if (!empty($question_id)) {
        // Update existing question
        $query = "UPDATE question SET 
                    question='$question', 
                    op1='$op1', 
                    op2='$op2', 
                    op3='$op3', 
                    op4='$op4', 
                    correct_option='$correct_option', 
                    ques_mark='$ques_mark' 
                  WHERE question_id='$question_id'";
        mysqli_query($conn, $query);
        echo "<script>alert('Question updated successfully!'); window.location.href='add_questions.php?exam_id=$exam_id&subject_id=$subject_id';</script>";
    } else {
        // Insert new question
        $query = "INSERT INTO question (exam_id, subject_id, question, op1, op2, op3, op4, correct_option, ques_mark) 
                  VALUES ('$exam_id', '$subject_id', '$question', '$op1', '$op2', '$op3', '$op4', '$correct_option', '$ques_mark')";
        mysqli_query($conn, $query);
        echo "<script>alert('Question added successfully!'); window.location.href='add_questions.php?exam_id=$exam_id&subject_id=$subject_id';</script>";
    }
}


    // Fetch all questions for this exam
    $questions_query = "SELECT * FROM question WHERE exam_id = $exam_id AND subject_id = $subject_id";
    $questions_result = mysqli_query($conn, $questions_query);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Questions</title>
        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
    </head>

    <body>
        <div class="container mt-4">
        <h1 style="color:black !important; display:block !important; visibility:visible !important;">Exam: <?= htmlspecialchars($exam_title) ?></h1>

            <h2>Add Questions for Exam ID: <?= $exam_id ?> | Subject ID: <?= $subject_id ?></h2>
            <h3>Current Questions: <?= $question_count ?>/<?= $question_limit ?></h3>
            <!-- <p>meet</p> -->
             <!-- <h1>meet</h1> -->

            <!-- Upload Excel Form -->
            <form method="post" enctype="multipart/form-data" class="mb-3">
                <input type="file" name="excel_file" required class="form-control">
                <button type="submit" name="upload" class="btn btn-primary mt-2">Upload</button>
            </form>

            <!-- Show All Questions -->
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Question</th>
                        <th>Option A</th>
                        <th>Option B</th>
                        <th>Option C</th>
                        <th>Option D</th>
                        <th>Correct Option</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($questions_result)) { ?>
                    <tr id="row-<?= $row['question_id'] ?>">
                        <td><?= $row['question'] ?></td>
                        <td><?= $row['op1'] ?></td>
                        <td><?= $row['op2'] ?></td>
                        <td><?= $row['op3'] ?></td>
                        <td><?= $row['op4'] ?></td>
                        <td><?= strtoupper($row['correct_option']) ?></td>
                        <td><?= $row['ques_mark'] ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm"
                                onclick="editQuestion(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <button class="btn btn-danger btn-sm"
                                onclick="deleteQuestion(<?= $row['question_id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Add Question Button -->
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#questionModal">Add Question</button>

            <!-- Bootstrap Add Question Modal -->
            <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <input type="hidden" name="question_id" id="question_id">
                                <div class="mb-3">
                                    <label>Question:</label>
                                    <input type="text" name="question" id="question" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Option A:</label>
                                    <input type="text" name="op1" id="op1" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Option B:</label>
                                    <input type="text" name="op2" id="op2" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Option C:</label>
                                    <input type="text" name="op3" id="op3" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Option D:</label>
                                    <input type="text" name="op4" id="op4" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Correct Option:</label>
                                    <select name="correct_option" id="correct_option" class="form-select" required>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Marks:</label>
                                    <input type="number" name="ques_mark" id="ques_mark" class="form-control" required>
                                </div>
                                <button type="submit" name="add_question" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fw-bold text-center fs-2 p-5">
            <<<...back to the <a href="faculty_dashboard.php">Dashboard</a>
        </div>

        <!-- Bootstrap & AJAX -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        function editQuestion(questionData) {
            document.getElementById("question_id").value = questionData.question_id;
            document.getElementById("question").value = questionData.question;
            document.getElementById("op1").value = questionData.op1;
            document.getElementById("op2").value = questionData.op2;
            document.getElementById("op3").value = questionData.op3;
            document.getElementById("op4").value = questionData.op4;
            document.getElementById("correct_option").value = questionData.correct_option;
            document.getElementById("ques_mark").value = questionData.ques_mark;

            new bootstrap.Modal(document.getElementById("questionModal")).show();
        }


        function deleteQuestion(id) {
            if (confirm("Are you sure you want to delete this question?")) {
                fetch("delete_question.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `question_id=${id}` // Fix: Use "question_id" instead of "id"
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() === "success") {
                            document.getElementById("row-" + id).remove();
                            alert("Question deleted successfully!");
                        } else {
                            alert("Failed to delete question.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        }
        console.log(document.querySelector("h2"));

        </script>
    </body>

    </html>