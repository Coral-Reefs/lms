<?php
require_once '../connection.php';
$email = $_POST["email"];
$username = $_POST["username"];
$isTeacher = (bool)$_POST['role'];
$password = $_POST["password"];
$password2 = $_POST["password2"];

$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$errors = 0;


// if(strlen($username) < 8) {
//     $errors++;
//     echo "<h4>Username must be atleast 8 characters</h4>";
// }

if(strlen($password) < 8 || strlen($password2) < 8) {
    $errors++;
    echo "<h4>Password must be atleast 8 characters</h4>";
}

if($password != $password2) {
    $errors++;
    echo "<h4>Password and Confirm Password should match</h4>";
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors++;
    echo "<h4>$email is not a valid email address</h4>";
}

if($username) {
    $query = "SELECT name FROM users WHERE name = '$username'";
    $result = mysqli_fetch_assoc(mysqli_query($cn, $query));

    if($result) {
        echo "<h4>Username is already taken</h4>";
        $errors++;
        mysqli_close($cn);
    }
}

if($errors > 0) {
    echo "<a href='/views/pages/register.php'>Go back to register</a>";
}

if($errors === 0) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email, name, password, isTeacher) VALUES ('$email', '$username', '$password', '$isTeacher');";
    mysqli_query($cn, $query);
    mysqli_close($cn);
    
    session_start();
    // $_SESSION['class'] = "success";
    // $_SESSION['message'] = "Register Successfully";
    header("Location: /");
}
