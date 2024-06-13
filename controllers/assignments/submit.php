
<?php
require_once "../connection.php";
session_start();

$post_id = $_POST['post_id'];
echo $post_id;
$user_id = $_SESSION['user_info']['id'];

$query_check = "SELECT id FROM submissions WHERE user_id = $user_id AND post_id = $post_id";
$result_check = mysqli_query($cn, $query_check);
$submission_id = 0;
if(mysqli_num_rows($result_check)>0){
    echo '1';
    $submission_id = mysqli_fetch_assoc($result_check)['id'];
    $query = "UPDATE submissions SET date = '$date', status = 1 WHERE id = $submission_id ";
    mysqli_query($cn, $query);

}else{
    echo '2';
    $query = "INSERT INTO submissions (date, status, user_id, post_id) VALUES ('$date', 1, '$user_id', '$post_id') ";
    mysqli_query($cn, $query);
    $submission_id = mysqli_insert_id($cn);
}


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

