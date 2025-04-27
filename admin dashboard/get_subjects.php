<?php
include 'db.php'; // Database connection

$query = "SELECT subject_id, subject_name FROM subject";
$result = $conn->query($query);

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= '<option value="' . $row['subject_id'] . '">' . $row['subject_name'] . '</option>';
}
echo $options;
?>
