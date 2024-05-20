<?php
//Edit the following fields in order to establish a connection with MySQL
//********************************************************************************
$dbhost	= "php-website.cz0wmokvgynm.us-east-1.rds.amazonaws.com"; //Leave this as 'localhost' once uploaded on a server
$dbuser	= "admin"; //Username that is allowed to access the database
$dbpass	= "akshith31"; //Password
$dbname	= "twitter"; //Name of the database
//********************************************************************************

$conn = new mysqli($dbhost, $dbuser, $dbpass,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>