<?php
declare(strict_types=1);

use Nexera\Config;

require_once __DIR__ . '/../config/constants.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(Config\SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => Config\SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/**
 * Regenerate the current session ID to mitigate fixation attacks.
 */
function regenerate_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Enforce session timeout.
 */
function enforce_session_timeout(): void
{
    $now = time();
    $lastActivity = $_SESSION['last_activity'] ?? $now;

    if (($now - $lastActivity) > Config\SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }

    $_SESSION['last_activity'] = $now;
}


