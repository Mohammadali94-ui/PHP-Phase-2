<?php
require_once '../config/db.php';
require_once '../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);

    if ($stmt->execute()) {
        session_unset();
        session_destroy();

        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
</head>
<body>
    <h1>Delete Account</h1>

    <p>Are you sure you want to delete your account?</p>

    <form method="post">
        <button type="submit">Yes, Delete My Account</button>
    </form>

    <p><a href="profile.php">Cancel</a></p>
</body>
</html>