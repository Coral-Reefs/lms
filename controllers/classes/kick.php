<?php
require_once '../connection.php';
session_start();

$student_id = $_POST['student_id'];

$query = "DELETE FROM students WHERE id = $student_id";
mysqli_query($cn, $query);
mysqli_close($cn);

header("Location: $_SERVER[HTTP_REFERER]");
