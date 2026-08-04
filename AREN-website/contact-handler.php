<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $message === '') {
    http_response_code(400);
    exit('Please complete the required fields.');
}

$to = 'info.afrirenewableenergy@gmail.com';
$subject = 'New quotation request from AREN website';
$body = "First Name: $firstName\nLast Name: $lastName\nEmail: $email\nService: $service\n\nMessage:\n$message";
$headers = "From: $email\r\nReply-To: $email\r\n";

mail($to, $subject, $body, $headers);

// In a real Django setup, this is where you would save the submission to a database.
// For now, this returns a simple success response.
header('Location: thank-you.html');
exit;
