<?php
declare(strict_types=1);

use Nexera\Config;

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/session.php';

/**
 * Returns the CSRF token for the current session, generating a new one if needed.
 */
function csrf_token(): string
{
    if (empty($_SESSION[Config\CSRF_TOKEN_KEY])) {
        $_SESSION[Config\CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
    }

    return $_SESSION[Config\CSRF_TOKEN_KEY];
}

/**
 * Validates a CSRF token from a request (POST/GET).
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token(?string $token): bool
{
    if (!$token || empty($_SESSION[Config\CSRF_TOKEN_KEY])) {
        return false;
    }

    $isValid = hash_equals($_SESSION[Config\CSRF_TOKEN_KEY], $token);

    if ($isValid) {
        unset($_SESSION[Config\CSRF_TOKEN_KEY]);
    }

    return $isValid;
}


