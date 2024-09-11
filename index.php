<?php
include 'db.php';

// Fetch all students
$students = $pdo->query("SELECT * FROM Student")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPA System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Student List</h1>
    <ul>
        <?php foreach ($students as $student): ?>
            <li><a href="student_detail.php?id=<?= $student['student_id'] ?>"><?= $student['name'] ?> - <?= $student['department'] ?></a></li>
        <?php endforeach; ?>
    </ul>

    <a href="add_student.php">Add Student</a>
    <a href="add_course.php">Add Course</a>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
