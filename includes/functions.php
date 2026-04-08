<?php
// includes/functions.php

function project_root(): string {
  return dirname(__DIR__); // one level above /includes
}

function load_env(string $path): void {
  if (!file_exists($path)) return;

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;

    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) continue;

    $key = trim($parts[0]);
    $val = trim($parts[1]);

    // Strip quotes if present
    $val = trim($val, "\"'");

    // Don't overwrite existing env vars
    if (getenv($key) === false) {
      putenv("$key=$val");
      $_ENV[$key] = $val;
    }
  }
}

function e(string $text): string {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never {
  header("Location: $path");
  exit;
}

function require_post(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
  }
}

// reCAPTCHA v2 checkbox verification (server-side)
function verify_recaptcha(string $token): bool {
  $secret = getenv('RECAPTCHA_SECRET_KEY') ?: '';
  if ($secret === '') return false;

  $data = http_build_query([
    'secret' => $secret,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
  ]);

  $opts = [
    'http' => [
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'content' => $data,
      'timeout' => 10
    ]
  ];

  $context = stream_context_create($opts);
  $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
  if ($result === false) return false;

  $json = json_decode($result, true);
  return isset($json['success']) && $json['success'] === true;
}
