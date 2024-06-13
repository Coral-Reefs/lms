<?php
require_once '../connection.php';

session_start();

$name = mysqli_real_escape_string($cn, $_POST['name']);
$description = mysqli_real_escape_string($cn, $_POST['desc']);
$current_date = date("d M Y");
$current_time = date("H:i:s");
$date = strtotime($current_date . $current_time);
$user = $_SESSION['user_info']['id'];

var_dump($name, $description, $user);

function generateUniqueId($cn) {
    do {
        $id = substr(str_shuffle(str_repeat("01234567890123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", 6)), 0, 6);

        $query_search = "SELECT id FROM classes WHERE id = '$id'";
        $result = mysqli_query($cn, $query_search);
    } while (mysqli_num_rows($result) > 0);

    return $id;
}

$id = generateUniqueId($cn);

$query = "INSERT INTO classes (id, name, description, create_date, owner_id) VALUES
('$id','$name','$description', $date, $user)";

mysqli_query($cn, $query);
mysqli_close($cn);

header('Location: /');