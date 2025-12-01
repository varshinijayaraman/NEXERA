<?php
declare(strict_types=1);

use Nexera\Config;

require_once __DIR__ . '/../config/constants.php';

/**
 * Sanitize output for safe HTML rendering.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect helper.
 */
function redirect(string $path): void
{
    header("Location: {$path}");
    exit();
}

/**
 * Ensures the current user is authenticated and optionally checks for role.
 */
function require_login(?string $role = null): void
{
    require_once __DIR__ . '/session.php';

    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }

    if ($role !== null && ($_SESSION['user']['role'] ?? null) !== $role) {
        redirect('login.php?unauthorized=1');
    }
}

/**
 * Role-based check helper.
 */
function is_role(string $role): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

/**
 * Returns the current authenticated user array or null.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Force password change for students on first login.
 */
function require_password_change(): void
{
    if (!empty($_SESSION['force_password_change']) && $_SESSION['force_password_change'] === true) {
        if (basename($_SERVER['PHP_SELF']) !== 'change_password.php') {
            redirect('change_password.php');
        }
    }
}

/**
 * Map staff teaching boolean to descriptive string.
 */
function staff_type_label(bool $teaching): string
{
    return $teaching ? 'Teaching Staff' : 'Non-Teaching Staff';
}

/**
 * Generate a random filename preserving extension.
 */
function generate_safe_filename(string $originalName): string
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return bin2hex(random_bytes(16)) . ($extension ? ".{$extension}" : '');
}


