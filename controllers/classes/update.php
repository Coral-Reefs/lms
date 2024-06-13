<?php
require_once '../connection.php';
session_start();

if(isset($_SESSION) && !$_SESSION['user_info']['isTeacher']){
    echo 'You cannot perform this action';
}
$name = mysqli_real_escape_string($cn, $_POST['name']);
$description = mysqli_real_escape_string($cn, $_POST['desc']);
$id = $_POST['id'];

$details_query = "UPDATE classes SET name = '$name', description = '$description' WHERE id = '$id';";
mysqli_query($cn, $details_query);
mysqli_close($cn);
header("Location: $_SERVER[HTTP_REFERER]");