<?php
require_once '../connection.php';

session_start();

$user = $_SESSION['user_info']['id'];
$code1 = $_POST['code1'];
$code2 = $_POST['code2'];
$code3 = $_POST['code3'];
$code4 = $_POST['code4'];
$code5 = $_POST['code5'];
$code6 = $_POST['code6'];
$class_code = $code1 . $code2 . $code3 . $code4 . $code5 . $code6;

$query_check = "SELECT id FROM classes WHERE id = '$class_code'";
if(mysqli_num_rows(mysqli_query($cn, $query_check)) == 0){ ?>
    <script>
        alert("That's not a valid code");
        window.location.replace("/");
    </script>
<?php
    die();
}

$query_check = "SELECT id FROM students WHERE user_id = $user AND class_id = '$class_code'";
if(mysqli_num_rows(mysqli_query($cn, $query_check)) > 0){ ?>
    <script>
        alert("You're already in that class");
        window.location.replace("/");
    </script>
<?php
    die();
}


$query = "INSERT INTO students (user_id, class_id) VALUES
($user, '$class_code')";

mysqli_query($cn, $query);
mysqli_close($cn);

header("Location: $_SERVER[HTTP_REFERER]");