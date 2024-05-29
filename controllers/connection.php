<?php 
// mysqli('hostname', 'dbUsername', 'dbPassword', 'dbName');
$cn = mysqli_connect("localhost", "root", "", "lms");

//Check connection
if(mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ". mysqli_connect_error();
    die();
};