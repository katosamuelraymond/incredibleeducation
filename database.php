<?php
$host = "sql303.byethost3.com";      
$user = "b3_38677495";           
$password = "0752084847sam";           
$dbname = "b3_38677495_incredibleeducation";    

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
