<?php
session_start();
include 'db.php'; // Ensure this file contains your database connection

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

// Fetch student information
$student_id = $_SESSION['user_id'];
try {
    // Fetch courses the student is enrolled in
    $stmt = $pdo->prepare("SELECT e.*, c.course_name, c.credit_hours 
                           FROM Enrollment e 
                           JOIN Course c ON e.course_id = c.course_id 
                           WHERE e.student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching courses: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses</title>
</head>
<body>
    <h1>Your Enrolled Courses</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($courses)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Grade</th>
                    <th>Credit Hours</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                        <td><?= htmlspecialchars($course['grade']) ?></td>
                        <td><?= htmlspecialchars($course['credit_hours']) ?></td>
                        <td><?= htmlspecialchars($course['semester']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not enrolled in any courses.</p>
    <?php endif; ?>
    <li><a href="enroll_student.php">Enlorement</a></li>
   

    <a href="dashboard.php">Back to Dashboard</a>
    <a href="logout.php">Logout</a>
</body>
</html>
