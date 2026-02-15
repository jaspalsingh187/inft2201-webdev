<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');

try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$mail = new Mail($pdo);
$page = new Page();

$method = $_SERVER['REQUEST_METHOD'];

// Extract ID from URL: /api/mail/123
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

if (!ctype_digit($id)) {
    $page->badRequest();
    exit;
}

$id = (int)$id;

if ($method === 'GET') {
    $item = $mail->getMail($id);
    if (!$item) {
        $page->notFound();
        exit;
    }
    $page->item($item);
    exit;
}

if ($method === 'PUT') {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!is_array($data) || !isset($data['subject']) || !isset($data['body'])) {
        $page->badRequest();
        exit;
    }

    $ok = $mail->updateMail($id, $data['subject'], $data['body']);
    if (!$ok) {
        $page->notFound();
        exit;
    }

    $page->item(["updated" => true]);
    exit;
}

if ($method === 'DELETE') {
    $ok = $mail->deleteMail($id);
    if (!$ok) {
        $page->notFound();
        exit;
    }

    $page->item(["deleted" => true]);
    exit;
}

$page->badRequest();
