<?php
session_start();

function sanitize_text($value) {
    return trim(strip_tags($value));
}

function sanitize_email($value) {
    return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
}

function write_submission($payload) {
    $directory = __DIR__ . '/data';
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $file = $directory . '/submissions.jsonl';
    $line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$first_name = sanitize_text($_POST['first_name'] ?? '');
$last_name = sanitize_text($_POST['last_name'] ?? '');
$email = sanitize_email($_POST['email'] ?? '');
$service = sanitize_text($_POST['service'] ?? '');
$message = sanitize_text($_POST['message'] ?? '');

$errors = [];

if ($first_name === '') { $errors[] = 'First name is required.'; }
if ($last_name === '') { $errors[] = 'Last name is required.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email is required.'; }
if ($service === '') { $errors[] = 'Please select a service.'; }
if ($message === '') { $errors[] = 'Please describe your project.'; }

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: index.html#contact');
    exit;
}

$submission = [
    'id' => bin2hex(random_bytes(4)),
    'created_at' => gmdate('c'),
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'service' => $service,
    'message' => $message,
];

write_submission($submission);

$_SESSION['form_success'] = true;
header('Location: thank-you.html');
exit;
