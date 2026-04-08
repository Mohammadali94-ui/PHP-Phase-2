<?php
require_once '../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>

    <p>You are logged in.</p>

    <p><a href="index.php">Go to Home</a></p>
    <p><a href="uploads.php">Upload a File</a></p>
    <p><a href="edit_account.php">Edit Account</a></p>
    <p><a href="delete_account.php">Delete Account</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>