<?php
$host = 'localhost';
$dbname = 'pizza_db';
$username = 'root';
$password = ''; // Default XAMPP password is empty

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>