<?php
session_start();
require_once '../connection.php';
$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE email = '$email';";
$result = mysqli_query($cn, $query);
$user = mysqli_fetch_array($result);

if ($user && password_verify($password, $user['password'])){
    $_SESSION['user_info'] = $user;
    // $_SESSION['class'] = 'success';
    // $_SESSION['message'] = 'Login Successfully';
    mysqli_close($cn);
    header('Location: /');
}else{
    echo '<h4>Invalid credentials</h4>';
    echo "<a href='/views/pages/login.php'>Go back to login</a>";

}