<?php
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog</title>
</head>
<body>

<h1>My Blog</h1>

<?php if (count($posts) === 0): ?>
    <p>No posts yet.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <small><?= $post['created_at'] ?></small>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
