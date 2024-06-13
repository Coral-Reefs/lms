<?php
// var_dump($_POST);
// var_dump($_FILES);

require_once "../connection.php";
$title = mysqli_escape_string($cn, $_POST['title']);
$body = mysqli_real_escape_string($cn, $_POST['body']);
$class_id = $_POST['class_id'];

$marks = ($_POST['marks'] === '' || !isset($_POST['marks'])) ? NULL : $_POST['marks'];
$due = strtotime((isset($_POST['duedate']) ? $_POST['duedate'] : NULL) . (isset($_POST['duetime']) ? $_POST['duetime'] : NULL));

$query = "INSERT INTO posts (title, body, date, class_id, marks, due) VALUES ('$title', '$body', '$date', '$class_id', ";
$query .= is_null($marks) ? "NULL" : "'$marks'";
$query .= ", ";
$query .= is_null($due) ? "NULL" : "'$due'";
$query .= ")";

mysqli_query($cn, $query);
$post_id = mysqli_insert_id($cn);

echo "<pre>";
var_dump($_FILES);
echo "</pre>";

// upload files
$files = $_FILES['files'];
for ($i = 0; $i < count($files['name']); $i++) {
    $file_name = mysqli_real_escape_string($cn, basename($files['name'][$i]));

    $file_path = __DIR__ . '/../../assets/public/files/' . basename($files['name'][$i]);
    if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
        $query = "INSERT INTO post_files (file_path, post_id) VALUES ('$file_name', '$post_id')";
        echo $query;
        mysqli_query($cn, $query);
    }
}

// Redirect or success message
header("Location: $_SERVER[HTTP_REFERER]");
exit();
