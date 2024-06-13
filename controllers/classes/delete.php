<?php
require_once '../connection.php';
session_start();

$id = $_POST['id'];
$confirm = $_POST['confirm'];

// $img_query = "SELECT image FROM products WHERE id = $id";
// $result = mysqli_query($cn, $img_query);
// $img_path = mysqli_fetch_assoc($result);
// $file_path = "../..".$img_path["image"];
// if(file_exists($file_path)){
//     unlink($file_path);
// }

$name_query = "SELECT name FROM classes WHERE id = '$id'";
$result = mysqli_query($cn, $name_query);
$name = mysqli_fetch_assoc($result)['name'];
if($confirm == $name){
    //delete posts
    $query = "DELETE FROM students WHERE class_id = '$id'";
    mysqli_query($cn, $query);


    //delete class
    $query = "DELETE FROM classes WHERE id = '$id'";
    mysqli_query($cn, $query);
    mysqli_close($cn);

    header("Location: $_SERVER[HTTP_REFERER]");
}else{
    echo '<h3>Class name does not match confirm input</h3>';
    echo "<a href='/'>Go back</a>";
    
}
