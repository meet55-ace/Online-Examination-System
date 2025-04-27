<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../db.php';
include '../link.php';

$exam_id = $_GET['exam_id'] ?? null;
$subject_id = null;
$questions = [];

if ($exam_id) {
    $exam_query = "SELECT subject_id FROM exam WHERE exam_id = ?";
    $exam_stmt = $conn->prepare($exam_query);
    $exam_stmt->bind_param("i", $exam_id);
    $exam_stmt->execute();
    $exam_result = $exam_stmt->get_result();
    $exam_data = $exam_result->fetch_assoc();
    $subject_id = $exam_data['subject_id'] ?? null;

    $query = "SELECT question_id, question, op1, op2, op3, op4, correct_option, ques_mark FROM question WHERE exam_id = ?";
    if ($subject_id) {
        $query .= " AND subject_id = ?";
    }

    $stmt = $conn->prepare($query);
    if ($subject_id) {
        $stmt->bind_param("ii", $exam_id, $subject_id);
    } else {
        $stmt->bind_param("i", $exam_id);
    }

    if ($stmt->execute()) {
        $questions = $stmt->get_result();
    } else {
        $questions = null;
    }
}

// Add Question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_question'])) {
    $exam_id = $_POST['exam_id'];
    $subject_id = $_POST['subject_id'];
    $question_text = $_POST['question'];
    $option_a = $_POST['op1'];
    $option_b = $_POST['op2'];
    $option_c = $_POST['op3'];
    $option_d = $_POST['op4'];
    $correct_option = $_POST['correct_option'];
    $ques_mark = $_POST['ques_mark'];

    $query = "INSERT INTO question (exam_id, subject_id, question, op1, op2, op3, op4, correct_option, ques_mark) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iissssssi", $exam_id, $subject_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $ques_mark);

    if ($stmt->execute()) {
        $question_id = $stmt->insert_id; // Get the last inserted question ID.

        // Insert into answer_key table
        $answer_key_query = "INSERT INTO answer_key (question_id, correct_option) VALUES (?, ?)";
        $answer_key_stmt = $conn->prepare($answer_key_query);
        $answer_key_stmt->bind_param("is", $question_id, $correct_option);

        if ($answer_key_stmt->execute()) {
            echo "<script>
                alert('Question and answer key added successfully.');
                window.location.href = 'addQuestion.php?exam_id=$exam_id';
            </script>";
        } else {
            echo "<script>alert('Failed to add answer key.');</script>";
        }
    } else {
        echo "<script>alert('Failed to add question.');</script>";
    }
}



// Update Question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_question'])) {
    $question_id = $_POST['question_id'];
    $updated_question = $_POST['updated_question'];
    $updated_op1 = $_POST['updated_op1'];
    $updated_op2 = $_POST['updated_op2'];
    $updated_op3 = $_POST['updated_op3'];
    $updated_op4 = $_POST['updated_op4'];
    $updated_correct_option = $_POST['updated_correct_option'];
    $updated_ques_mark = $_POST['updated_ques_mark'];

    $query = "UPDATE question SET question = ?, op1 = ?, op2 = ?, op3 = ?, op4 = ?, correct_option = ?, ques_mark = ? WHERE question_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssii", $updated_question, $updated_op1, $updated_op2, $updated_op3, $updated_op4, $updated_correct_option, $updated_ques_mark, $question_id);

    if ($stmt->execute()) {
        // Update the answer_key table
        $answer_key_query = "UPDATE answer_key SET correct_option = ? WHERE question_id = ?";
        $answer_key_stmt = $conn->prepare($answer_key_query);
        $answer_key_stmt->bind_param("si", $updated_correct_option, $question_id);

        if ($answer_key_stmt->execute()) {
            echo "<script>alert('Question and answer key updated successfully!');</script>";
            echo "<script>window.location.href = 'addQuestion.php?exam_id=$exam_id';</script>";
        } else {
            echo "<script>alert('Error updating answer key.');</script>";
        }
    } else {
        echo "<script>alert('Error updating question.');</script>";
    }
}



// Delete Question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id_to_delete'];

    $stmt = $conn->prepare("DELETE FROM question WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Question deleted successfully.', 'questions' => fetchQuestions($exam_id, $subject_id)]);
    } else {
        echo json_encode(['error' => 'Failed to delete question.']);
    }
    exit;
}

function fetchQuestions($exam_id, $subject_id)
{
    global $conn;
    $query = "SELECT question_id, question, op1, op2, op3, op4, correct_option, ques_mark FROM question WHERE exam_id = ?";
    if ($subject_id) {
        $query .= " AND subject_id = ?";
    }

    $stmt = $conn->prepare($query);
    if ($subject_id) {
        $stmt->bind_param("ii", $exam_id, $subject_id);
    } else {
        $stmt->bind_param("i", $exam_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    return $questions;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <style>
        .container {
            display: flex;
            gap: 20px;
        }

        .form-container {
            flex: 1;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
        }

        .question-list {
            flex: 1;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            overflow-y: auto;
            max-height: 500px;
        }

        .question-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .question-item h5 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .question-item p {
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <!-- add new quetion -->
    <h3 class="fw-bold mb-4 text-center pt-3 pb-3 bg-light">Add New Question</h3>
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">

                <div class="mb-3">
                    <label for="question_text" class="form-label">Question Text</label>
                    <textarea id="question_text" name="question" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="option_a" class="form-label">Option A</label>
                    <input type="text" id="option_a" name="op1" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="option_b" class="form-label">Option B</label>
                    <input type="text" id="option_b" name="op2" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="option_c" class="form-label">Option C</label>
                    <input type="text" id="option_c" name="op3" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="option_d" class="form-label">Option D</label>
                    <input type="text" id="option_d" name="op4" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="correct_option" class="form-label">Correct Option</label>
                    <select id="correct_option" name="correct_option" class="form-select" required>
                        <option value="" disabled>Select Correct Option</option>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ques_mark" class="form-label">Question Marks</label>
                    <input type="number" id="ques_mark" name="ques_mark" class="form-control" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Add Question</button>
                    <a href="editExam.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <!-- existing quetion -->
        <div class="question-list">
            <h4>Existing Questions</h4>
            <?php if ($questions && $questions->num_rows > 0) : ?>
                <?php while ($question = $questions->fetch_assoc()) : ?>
                    <div class="question-item">
                        <h5><?php echo htmlspecialchars($question['question']); ?></h5>
                        <p><strong>A:</strong> <?php echo htmlspecialchars($question['op1']); ?></p>
                        <p><strong>B:</strong> <?php echo htmlspecialchars($question['op2']); ?></p>
                        <p><strong>C:</strong> <?php echo htmlspecialchars($question['op3']); ?></p>
                        <p><strong>D:</strong> <?php echo htmlspecialchars($question['op4']); ?></p>
                        <p><strong>Correct:</strong> <?php echo htmlspecialchars($question['correct_option']); ?></p>
                        <p><strong>Marks:</strong> <?php echo htmlspecialchars($question['ques_mark']); ?></p>

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $question['question_id']; ?>">Edit</button>
                        <button onclick="deleteQuestion(<?php echo $question['question_id']; ?>)" class="btn btn-danger">Delete</button>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $question['question_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Question</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                                        <div class="mb-3">
                                            <label for="updated_question" class="form-label">Question Text</label>
                                            <textarea name="updated_question" class="form-control" rows="3" required><?php echo htmlspecialchars($question['question']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_op1" class="form-label">Option A</label>
                                            <input type="text" name="updated_op1" class="form-control" value="<?php echo htmlspecialchars($question['op1']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_op2" class="form-label">Option B</label>
                                            <input type="text" name="updated_op2" class="form-control" value="<?php echo htmlspecialchars($question['op2']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_op3" class="form-label">Option C</label>
                                            <input type="text" name="updated_op3" class="form-control" value="<?php echo htmlspecialchars($question['op3']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_op4" class="form-label">Option D</label>
                                            <input type="text" name="updated_op4" class="form-control" value="<?php echo htmlspecialchars($question['op4']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_correct_option" class="form-label">Correct Option</label>
                                            <select name="updated_correct_option" class="form-select" required>
                                                <option value="A" <?php echo ($question['correct_option'] == 'A') ? 'selected' : ''; ?>>Option A</option>
                                                <option value="B" <?php echo ($question['correct_option'] == 'B') ? 'selected' : ''; ?>>Option B</option>
                                                <option value="C" <?php echo ($question['correct_option'] == 'C') ? 'selected' : ''; ?>>Option C</option>
                                                <option value="D" <?php echo ($question['correct_option'] == 'D') ? 'selected' : ''; ?>>Option D</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updated_ques_mark" class="form-label">Question Marks</label>
                                            <input type="number" name="updated_ques_mark" class="form-control" value="<?php echo htmlspecialchars($question['ques_mark']); ?>" required>
                                        </div>
                                        <button type="submit" name="update_question" class="btn btn-success">Update Question</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No questions available for this exam and subject.</p>
            <?php endif; ?>
        </div>

    </div>

    <div class="fw-bold text-center fs-2 p-5">
        <<<...back to the <a href="dashboard.php">Dashboard</a>
    </div>

    <script>
        $(document).ready(function() {
            $(document).on('submit', '.updateQuestionForm', function(e) {
                e.preventDefault();

                $.ajax({
                    url: 'editquetion.php', 
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        var data = JSON.parse(response);

                        if (data.error) {
                            alert(data.error);
                        } else {
                            alert('Question updated successfully!');
                            loadQuestions(data);
                        }
                    },
                    error: function() {
                        alert('Error updating question.');
                    }
                });
            });

            // Function to load updated questions dynamically
            function loadQuestions(questions) {
                var questionHtml = '';
                if (questions.length > 0) {
                    questions.forEach(function(question) {
                        questionHtml += '<div class="question-item">';
                        questionHtml += '<h5>' + question.question + '</h5>';
                        questionHtml += '</div>';
                    });
                } else {
                    questionHtml = '<p>No questions available.</p>';
                }

                $('#existingQuestions').html(questionHtml);
            }
            loadQuestions([]);
        });

    // Delete Question Function
    function deleteQuestion(questionId) {
        if (confirm('Are you sure you want to delete this question?')) {
            $.ajax({
                url: 'editQuetion.php', 
                type: 'POST',
                data: {
                    delete_question: true, 
                    question_id_to_delete: questionId 
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.error) {
                        alert('Error: ' + data.error);
                    } else {
                        alert(data.success);
                        $('#question_' + questionId).remove(); 
                    }
                },
                error: function() {
                    alert('Error deleting the question.');
                }
            });
        }
    }


    </script>
</body>

</html>