<?php
require_once '../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === 0) {
        $fileName = basename($_FILES['uploaded_file']['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $targetFile)) {
                $message = "File uploaded successfully.";
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Only JPG, JPEG, PNG, GIF, PDF, and TXT files are allowed.";
        }
    } else {
        $message = "Please choose a file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
</head>
<body>
    <h1>Upload a File</h1>

    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="uploaded_file" required>
        <button type="submit">Upload</button>
    </form>

    <p><a href="profile.php">Back to Profile</a></p>
</body>
</html>