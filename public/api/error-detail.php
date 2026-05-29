<?php
require_once __DIR__ . '/../../src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\ErrorLog;

header('Content-Type: application/json');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$errorModel = new ErrorLog();
$error = $errorModel->findById($id);

if (!$error) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$error['occurred_at'] = date('d/m/Y', strtotime($error['occurred_at']));

echo json_encode($error);
