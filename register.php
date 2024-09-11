<?php
session_start();
include 'db.php'; // Ensure this file contains your database connection

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // Either 'student' or 'admin'

    if (!empty($username) && !empty($password) && in_array($role, ['student', 'admin'])) {
        try {
            // Check if the username already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $userExists = $stmt->fetchColumn();

            if ($userExists) {
                $error = 'Username already exists.';
            } else {
                // Hash the password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $stmt = $pdo->prepare("INSERT INTO Users (username, password, role) VALUES (:username, :password, :role)");
                $stmt->execute([
                    'username' => $username,
                    'password' => $passwordHash,
                    'role' => $role
                ]);

                $success = '<p style="color: green; font-weight: bold;">Registration successful! Your account has been created. You can now <a href="login.php">log in</a> to access your dashboard. If you encounter any issues, please contact support.</p>';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'All fields are required and role must be either student or admin.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="student">Student</option>
            <option value="admin">Administrator</option>
        </select>
        <br>
        <button type="submit">Register</button>
        already have an account?<a href="login.php">Login</a>
    </form>
    <?php if ($error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <?= $success ?>
    <?php endif; ?>
</body>
</html>
