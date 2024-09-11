<?php
$host = 'localhost';
$dbname = 'gpa_system';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo"connected" ;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
