<?php
require_once dirname(dirname(__DIR__)) . '/src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\ErrorLog;

header('Content-Type: application/json');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$id = (int)($body['id'] ?? 0);
$status = $body['status'] ?? '';

if ($id === 0 || !in_array($status, ['pending', 'resolved'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$errorModel = new ErrorLog();
$ok = $errorModel->updateStatus($id, $status);

echo json_encode(['success' => $ok]);
