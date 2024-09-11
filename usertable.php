<?php 
include("db.php");

try {
    // Prepare the SQL query for creating the Enrollment table
    $query = "CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL
)";

    // Execute the query using PDO
    $pdo->exec($query);
    echo "Table created successfully!";
} catch (PDOException $e) {
    // Handle errors by showing the message
    die("Could not create table: " . $e->getMessage());
}
?>
