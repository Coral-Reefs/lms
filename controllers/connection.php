<?php 
// mysqli('hostname', 'dbUsername', 'dbPassword', 'dbName');
date_default_timezone_set('Asia/Kuala_Lumpur');
$current_date = date("d M Y");
$current_time = date("H:i:s");
$date = strtotime($current_date . $current_time);

$cn = mysqli_connect("localhost", "root", "", "lms");

//Check connection
if(mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ". mysqli_connect_error();
    die();
};