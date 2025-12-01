<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/models/Document.php';

use Nexera\Models\Document;

require_login();
enforce_session_timeout();

$documentId = (int) ($_GET['id'] ?? 0);

if ($documentId <= 0) {
    http_response_code(400);
    exit('Invalid document identifier.');
}

$model = new Document();
$document = $model->findById($documentId);

if (!$document) {
    http_response_code(404);
    exit('Document not found.');
}

$role = $_SESSION['user']['role'] ?? '';
if (!in_array($document['visibility_role'], [$role, 'all'], true)) {
    http_response_code(403);
    exit('You are not authorised to access this document.');
}

$filePath = __DIR__ . '/' . ltrim($document['file_path'], '/');

if (!is_file($filePath)) {
    http_response_code(404);
    exit('File missing. Contact administrator.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($document['filename']) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;


