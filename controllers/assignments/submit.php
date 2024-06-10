
<?php
require_once "../connection.php";
session_start();

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_info']['id'];

$query = "INSERT INTO submissions (date, user_id, post_id) VALUES ('$date', '$user_id', '$post_id') ";

mysqli_query($cn, $query);
$submission_id = mysqli_insert_id($cn);

echo "<pre>";
var_dump($_FILES);
echo "</pre>";

// upload files
$files = $_FILES['files'];
for ($i = 0; $i < count($files['name']); $i++) {
    $file_name = mysqli_real_escape_string($cn, basename($files['name'][$i]));

    $file_path = __DIR__ . '/../../assets/public/files/' . basename($files['name'][$i]);
    if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
        $query = "INSERT INTO submission_files (file_path, submission_id) VALUES ('$file_name', '$submission_id')";
        echo $query;
        mysqli_query($cn, $query);
    }
}

// Redirect or success message
header("Location: $_SERVER[HTTP_REFERER]");
exit();
