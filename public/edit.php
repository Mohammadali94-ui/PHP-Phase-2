<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = "Edit Post";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  redirect('index.php');
}

$errors = [];
$siteKey = getenv('RECAPTCHA_SITE_KEY') ?: '';

// Load existing post
$stmt = $pdo->prepare("SELECT id, title, content FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
  redirect('index.php');
}

$title = $post['title'];
$content = $post['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $token = $_POST['g-recaptcha-response'] ?? '';

  if ($title === '' || strlen($title) < 3 || strlen($title) > 150) {
    $errors[] = "Title must be between 3 and 150 characters.";
  }
  if ($content === '' || strlen($content) < 10) {
    $errors[] = "Content must be at least 10 characters.";
  }

  // Require recaptcha on edit too (keeps it consistent)
  if (!verify_recaptcha($token)) {
    $errors[] = "reCAPTCHA verification failed. Please try again.";
  }

  if (count($errors) === 0) {
    $u = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
    $u->execute([
      ':title' => $title,
      ':content' => $content,
      ':id' => $id
    ]);

    redirect('index.php');
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-3">Edit Post</h1>

<?php if (count($errors) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" id="editForm" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input
      type="text"
      name="title"
      class="form-control"
      required
      minlength="3"
      maxlength="150"
      value="<?= e($title) ?>"
    >
  </div>

  <div class="mb-3">
    <label class="form-label">Content</label>
    <textarea
      name="content"
      class="form-control"
      rows="6"
      required
      minlength="10"
    ><?= e($content) ?></textarea>
  </div>

  <?php if ($siteKey === ''): ?>
    <div class="alert alert-warning">
      Missing RECAPTCHA_SITE_KEY in your .env file.
    </div>
  <?php else: ?>
    <div class="mb-3">
      <div class="g-recaptcha" data-sitekey="<?= e($siteKey) ?>"></div>
    </div>
  <?php endif; ?>

  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
  </div>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
  document.getElementById('editForm').addEventListener('submit', function (e) {
    const title = document.querySelector('input[name="title"]').value.trim();
    const content = document.querySelector('textarea[name="content"]').value.trim();

    if (title.length < 3 || title.length > 150) {
      alert('Title must be between 3 and 150 characters.');
      e.preventDefault();
      return;
    }
    if (content.length < 10) {
      alert('Content must be at least 10 characters.');
      e.preventDefault();
      return;
    }
    if (document.querySelector('.g-recaptcha')) {
      const resp = grecaptcha.getResponse();
      if (!resp) {
        alert('Please complete the reCAPTCHA.');
        e.preventDefault();
      }
    }
  });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
