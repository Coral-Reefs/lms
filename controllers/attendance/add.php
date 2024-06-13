<?php
require_once '../connection.php';
$attendance = isset($_POST['attendance']) ? $_POST['attendance'] : [];
$class_id = $_POST['class_id'];
$date = strtotime(date('d M Y'));

$query_clear = "DELETE FROM attendance WHERE date = '$date' AND class_id = '$class_id'";
mysqli_query($cn, $query_clear);

$query = "SELECT user_id FROM students WHERE class_id = '$class_id'";
$result = mysqli_query($cn, $query);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($students as $student){
    $user_id = $student['user_id'];
    $status = in_array($user_id, $attendance) ? 1 : 0;
    $query = "INSERT INTO attendance (user_id, class_id, date, status) 
    VALUES ($user_id, '$class_id', '$date', $status)";
    mysqli_query($cn, $query);
}
header("Location: $_SERVER[HTTP_REFERER]");