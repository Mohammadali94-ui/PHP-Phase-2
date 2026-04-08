<?php
require_once '../config/db.php';
require_once '../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = "";

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email.";
    }

    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $update->bindParam(':name', $name);
        $update->bindParam(':email', $email);
        $update->bindParam(':id', $userId);

        if ($update->execute()) {
            $_SESSION['user_name'] = $name;
            $success = "Account updated successfully.";
            $user['name'] = $name;
            $user['email'] = $email;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
</head>
<body>
    <h1>Edit Account</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required><br><br>

        <button type="submit">Update Account</button>
    </form>

    <p><a href="profile.php">Back to Profile</a></p>
</body>
</html>