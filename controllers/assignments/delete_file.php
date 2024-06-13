<?php
require_once '../connection.php';
session_start();

$id = $_GET['id'];

$files_query = "SELECT * FROM submission_files WHERE id = $id";
$result = mysqli_query($cn, $files_query);
$files = mysqli_fetch_assoc($result);
$file_path = "../../assets/public/files/".$file["file_path"];
if(file_exists($file_path)){
    unlink($file_path);
}

//delete files

$query = "DELETE FROM submission_files WHERE id = $id";
mysqli_query($cn, $query);
mysqli_close($cn);

header("Location: $_SERVER[HTTP_REFERER]");
