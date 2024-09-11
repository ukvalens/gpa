<?php
session_start();
include 'db.php'; // Ensure this file contains your database connection

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update website settings
    if (isset($_POST['update_settings'])) {
        $site_title = trim($_POST['site_title']);
        $contact_email = trim($_POST['contact_email']);

        try {
            $stmt = $pdo->prepare("INSERT INTO Settings (setting_key, value) VALUES 
                ('site_title', :site_title)
                ON DUPLICATE KEY UPDATE value = :site_title");
            $stmt->execute(['site_title' => $site_title]);

            $stmt = $pdo->prepare("INSERT INTO Settings (setting_key, value) VALUES 
                ('contact_email', :contact_email)
                ON DUPLICATE KEY UPDATE value = :contact_email");
            $stmt->execute(['contact_email' => $contact_email]);

            $success = 'Settings updated successfully.';
        } catch (PDOException $e) {
            $error = 'Error updating settings: ' . $e->getMessage();
        }
    }

    // Change admin password
    if (isset($_POST['change_password'])) {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (!empty($current_password) && !empty($new_password) && $new_password === $confirm_password) {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM Users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                $current_password_hash = $stmt->fetchColumn();

                if (password_verify($current_password, $current_password_hash)) {
                    // Hash the new password and update it
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE Users SET password = :password WHERE user_id = :user_id");
                    $stmt->execute(['password' => $new_password_hash, 'user_id' => $_SESSION['user_id']]);
                    $success = 'Password changed successfully.';
                } else {
                    $error = 'Current password is incorrect.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'All fields are required and new passwords must match.';
        }
    }
}

// Fetch current settings
try {
    $stmt = $pdo->query("SELECT * FROM Settings");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = array_column($settings, 'value', 'setting_key'); // Transform to associative array
} catch (PDOException $e) {
    $error = 'Error fetching settings: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
</head>
<body>
    <h1>Admin Settings</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <h2>Update Website Settings</h2>
    <form action="admin_settings.php" method="post">
        <label for="site_title">Site Title:</label>
        <input type="text" id="site_title" name="site_title" value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>" required>
        <br>
        <label for="contact_email">Contact Email:</label>
        <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" required>
        <br>
        <button type="submit" name="update_settings">Update Settings</button>
    </form>

    <h2>Change Admin Password</h2>
    <form action="admin_settings.php" method="post">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        <br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit" name="change_password">Change Password</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
