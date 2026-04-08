<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = "Delete Post";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');

// Load post for confirmation
$stmt = $pdo->prepare("SELECT id, title FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $del = $pdo->prepare("DELETE FROM posts WHERE id = :id");
  $del->execute([':id' => $id]);
  redirect('index.php');
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-3 text-danger">Delete Post</h1>

<div class="card card-body shadow-sm">
  <p class="mb-3">Are you sure you want to delete:</p>
  <p class="fw-semibold mb-4"><?= e($post['title']) ?></p>

  <form method="post" class="d-flex gap-2">
    <button class="btn btn-danger" type="submit">Yes, delete</button>
    <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
