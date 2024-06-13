<?php
require_once '../connection.php';
session_start();

$id = $_POST['id'];

$files_query = "SELECT * FROM post_files WHERE post_id = $id";
$result = mysqli_query($cn, $files_query);
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($files as $file){
    $file_path = "../../assets/public/files/".$file["file_path"];
    if(file_exists($file_path)){
        unlink($file_path);
    }
}

//delete files

$query = "DELETE FROM post_files WHERE post_id = $id";
mysqli_query($cn, $query);

$query = "DELETE FROM posts WHERE id = $id";
mysqli_query($cn, $query);
mysqli_close($cn);

header("Location: $_SERVER[HTTP_REFERER]");
