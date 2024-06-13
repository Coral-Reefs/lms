<?php
require_once '../connection.php';
$marks = $_POST['marks'];
$submission_id = $_POST['submission_id'];

$query = "UPDATE submissions SET marks = $marks WHERE id = $submission_id";
mysqli_query($cn, $query);
mysqli_close($cn);
header("Location: $_SERVER[HTTP_REFERER]");
