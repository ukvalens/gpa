<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Display different content based on user role
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <p>Role: <?= htmlspecialchars($role) ?></p>

    <?php if ($role == 'admin'): ?>
        <h2>Admin Dashboard</h2>
        <p>As an administrator, you have access to the following features:</p>
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="admin_settings.php">Admin Settings</a></li>
        </ul>
    <?php elseif ($role == 'student'): ?>
        <h2>Student Dashboard</h2>
        <p>As a student, you can access the following:</p>
        <ul>
            <li><a href="calculate_gpa.php">Calculate GPA</a></li>
            <li><a href="view_courses.php">View Courses</a></li>
            <li><a href="student_profile.php">Update Profile</a></li>
            
            
        </ul>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
