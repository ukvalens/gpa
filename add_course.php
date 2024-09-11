<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $credit_hours = $_POST['credit_hours'];

    $sql = "INSERT INTO Course (course_name, credit_hours) VALUES (:course_name, :credit_hours)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['course_name' => $course_name, 'credit_hours' => $credit_hours]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Add Course</h2>
    <form action="add_course.php" method="POST">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <input type="number" name="credit_hours" placeholder="Credit Hours" required>
        <button type="submit">Add Course</button>
    </form>
</body>
</html>
