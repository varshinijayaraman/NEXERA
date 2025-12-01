<?php
/**
 * Database configuration for NEXERA College Management System.
 * Update the constants below to match your MySQL credentials and host setup.
 */

declare(strict_types=1);

namespace Nexera\Config;

use PDO;
use PDOException;

// === Database credentials (update as needed for your environment) ===
const DB_HOST = '127.0.0.1';
const DB_NAME = 'nexera_db';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Returns a shared PDO connection instance.
 *
 * @return PDO
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $exception) {
        // In production you would log this instead of displaying the message.
        die('Database connection failed. Please contact the administrator.');
    }

    return $pdo;
}


