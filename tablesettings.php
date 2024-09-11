<?php 
include("db.php");

try {
    // Prepare the SQL query for creating the Enrollment table
    $query = "CREATE TABLE Settings (
    setting_key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL
)";

    // Execute the query using PDO
    $pdo->exec($query);
    echo "Table 'Enrollment' created successfully!";
} catch (PDOException $e) {
    // Handle errors by showing the message
    die("Could not create table: " . $e->getMessage());
}
?>
