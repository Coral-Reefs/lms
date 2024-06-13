<?php
require_once '../connection.php';
session_start();
$currPw = $_POST['currentPassword'];
$password = $_POST['newPassword'];
$password2 = $_POST['newPassword2'];
$id = $_POST['id'];

$errors = 0;

if(!password_verify($currPw, $_SESSION['user_info']['password'])){
    $errors++;
    echo "<h4>Wrong Password</h4>";
}

if($password != $password2) {
    $errors++;
    echo "<h4>Password and Confirm Password should match</h4>";
}

if(strlen($password) < 8 || strlen($password2) < 8) {
    $errors++;
    echo "<h4>Password must be atleast 8 characters</h4>";
}

if($errors > 0) {
    echo "<a href='/views/pages/profile.php'>Go back to profile</a>";
}else{
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = '$password' WHERE id = $id";
    mysqli_query($cn, $query);
    mysqli_close($cn);
    $_SESSION['user_info']['password'] = $password;
    header("Location: $_SERVER[HTTP_REFERER]");
}