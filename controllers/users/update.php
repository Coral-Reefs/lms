<?php
require_once '../connection.php';
session_start();

$id = $_POST['id'];
$name = $_POST['name'];

$img_name = $_FILES['image']['name'];
$img_size = $_FILES['image']['size'];
$img_tmpname = $_FILES['image']['tmp_name'];
$img_type = strtolower(pathinfo($img_name, PATHINFO_EXTENSION)); 
$img_path = "/assets/public/images/".time()."-".$img_name;

$extensions = ['jpg','jpeg','png','svg','gif'];
$is_img = false;

if(in_array($img_type, $extensions)) {
    $is_img = true;
}

if($is_img && $img_size>0){
    $query = "UPDATE users SET pfp = '$img_path' WHERE id = $id;";
    move_uploaded_file($img_tmpname, "../..".$img_path);
    mysqli_query($cn, $query);
    $_SESSION['user_info']['pfp'] = $img_path;
}

$details_query = "UPDATE users SET name = '$name' WHERE id= $id;";
mysqli_query($cn, $details_query);
mysqli_close($cn);
$_SESSION['user_info']['name'] = $name;
header("Location: /views/pages/profile.php");