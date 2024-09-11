<?php
session_start();
include 'db.php'; // Ensure this file contains your database connection

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        // Delete user
        $user_id = $_POST['user_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
        } catch (PDOException $e) {
            $error = 'Error deleting user: ' . $e->getMessage();
        }
    } elseif (isset($_POST['add_user'])) {
        // Add new user
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);

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

                    $success = 'User added successfully.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'All fields are required and role must be either student or admin.';
        }
    }
}

// Fetch users
try {
    $stmt = $pdo->query("SELECT * FROM Users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching users: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <h2>Existing Users</h2>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form action="manage_users.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add New User</h2>
    <form action="manage_users.php" method="post">
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
        <button type="submit" name="add_user">Add User</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
