<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $level = $_POST['level'];

    $sql = "INSERT INTO Student (name, department, level) VALUES (:name, :department, :level)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $name, 'department' => $department, 'level' => $level]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Add Student</h2>
    <form action="add_student.php" method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="department" placeholder="Department" required>
        <input type="number" name="level" placeholder="Level" required>
        <button type="submit">Add Student</button>
    </form>
</body>
</html>
