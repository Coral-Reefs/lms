<?php
require_once '../connection.php';
session_start();

if(isset($_SESSION) && !$_SESSION['user_info']['isTeacher']){
    echo 'You cannot perform this action';
}

$title = mysqli_real_escape_string($cn, $_POST['title']);
$body = mysqli_real_escape_string($cn, $_POST['body']);
$post_id = $_POST['post_id'];

var_dump($_FILES['files']['name']);

if(isset($_FILES['files']) && is_array($_FILES['files']['name']) && count($_FILES['files']['name']) > 0 && $_FILES['files']['name'][0] !== ''){
    $files_query = "SELECT * FROM post_files WHERE post_id = $post_id";
    $result = mysqli_query($cn, $files_query);
    $files_existing = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach($files_existing as $file){
        $file_path = "../../assets/public/files/".$file["file_path"];
        if(file_exists($file_path)){
            unlink($file_path);
        }
    }
    $files = $_FILES['files'];
    for ($i = 0; $i < count($files['name']); $i++) {
        $file_name = mysqli_real_escape_string($cn, basename($files['name'][$i]));

        $file_path = __DIR__ . '/../../assets/public/files/' . basename($files['name'][$i]);
        if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
            $query = "INSERT INTO post_files (file_path, post_id) VALUES ('$file_name', '$post_id')";
            echo $query;
            mysqli_query($cn, $query);
        }
    }
}
if(isset($_POST['marks'])){
    $marks = $_POST['marks'];
    $query = "UPDATE posts SET marks = $marks WHERE id = $post_id;";
    mysqli_query($cn, $query);
}
if(isset($_POST['duedate']) || isset($_POST['duetime'])){
    $due = strtotime((isset($_POST['duedate']) ? $_POST['duedate'] : NULL) . (isset($_POST['duetime']) ? $_POST['duetime'] : NULL));
    $query = "UPDATE posts SET due = $due WHERE id = $post_id;";
    mysqli_query($cn, $query);
}

$details_query = "UPDATE posts SET title = '$title', body = '$body' WHERE id = '$post_id';";
mysqli_query($cn, $details_query);
mysqli_close($cn);
header("Location: $_SERVER[HTTP_REFERER]");