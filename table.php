<?php 
include("db.php");

try {
    // Prepare the SQL query for creating the Enrollment table
    $query = "CREATE TABLE Enrollment (
        enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT,
        course_id INT,
        grade VARCHAR(2),
        semester VARCHAR(10),
        FOREIGN KEY (student_id) REFERENCES Student(student_id),
        FOREIGN KEY (course_id) REFERENCES Course(course_id)
    )";

    // Execute the query using PDO
    $pdo->exec($query);
    echo "Table 'Enrollment' created successfully!";
} catch (PDOException $e) {
    // Handle errors by showing the message
    die("Could not create table: " . $e->getMessage());
}
?>
