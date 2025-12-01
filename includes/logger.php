<?php
declare(strict_types=1);

use Nexera\Config;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';

/**
 * Basic audit logger that records key user actions into the audit_logs table.
 */
function audit_log(?int $userId, string $action): void
{
    $pdo = Nexera\Config\getPDO();

    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, timestamp, ip) VALUES (:user_id, :action, NOW(), :ip)');
    $stmt->execute([
        ':user_id' => $userId,
        ':action'  => $action,
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
    ]);
}


