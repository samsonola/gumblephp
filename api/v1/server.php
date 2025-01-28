<?php

$dbname = "gumble";
$username = "root";
$password = "";
$host = "localhost";

$conn = mysqli_connect($host,$username,$password, $dbname);
if($conn){
    // echo "Connected";
}else{
    echo "Not connected";
}