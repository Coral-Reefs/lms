<?php
require_once '../connection.php';
$submission_id = $_POST['submission_id'];
echo $submission_id;
$query = "UPDATE submissions SET status = 0 WHERE id = $submission_id";
mysqli_query($cn, $query);
header("Location: $_SERVER[HTTP_REFERER]");