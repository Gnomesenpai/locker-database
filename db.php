<?php
$servername="mariadb";
$username="";
$password="";
$dbname="lockers";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>