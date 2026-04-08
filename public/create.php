<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = "Create Post";

$errors = [];
$title = '';
$content = '';

$siteKey = getenv('RECAPTCHA_SITE_KEY') ?: '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $token = $_POST['g-recaptcha-response'] ?? '';

  // Server-side validation
  if ($title === '' || strlen($title) < 3 || strlen($title) > 150) {
    $errors[] = "Title must be between 3 and 150 characters.";
  }
  if ($content === '' || strlen($content) < 10) {
    $errors[] = "Content must be at least 10 characters.";
  }

  // reCAPTCHA server check
  if (!verify_recaptcha($token)) {
    $errors[] = "reCAPTCHA verification failed. Please try again.";
  }

  if (count($errors) === 0) {
    $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (:title, :content)");
    $stmt->execute([
      ':title' => $title,
      ':content' => $content
    ]);

    redirect('index.php');
  }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-3">Create Post</h1>

<?php if (count($errors) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" id="postForm" class="card card-body shadow-sm">
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
    <div class="form-text">3–150 characters.</div>
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
    <div class="form-text">At least 10 characters.</div>
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
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
  </div>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
  // Client-side validation + reCAPTCHA check
  document.getElementById('postForm').addEventListener('submit', function (e) {
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

    // Only check recaptcha if widget exists
    if (document.querySelector('.g-recaptcha')) {
      const resp = grecaptcha.getResponse();
      if (!resp) {
        alert('Please complete the reCAPTCHA.');
        e.preventDefault();
        return;
      }
    }
  });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
