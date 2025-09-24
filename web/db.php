<?php

// Database Information
$host = 'localhost';
$dbname = 'myapp';
$dbuser = 'root';
$dbpass = ''; 

// Database Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>