<?php
require_once '../connection.php';
session_start();

$class_id = $_POST['id'];
$user_id = $_SESSION['user_info']['id'];

$query = "DELETE FROM students WHERE class_id = '$class_id' AND user_id = $user_id";
mysqli_query($cn, $query);
mysqli_close($cn);

header("Location: $_SERVER[HTTP_REFERER]");
