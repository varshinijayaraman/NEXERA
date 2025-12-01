<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers/AuthController.php';

use Nexera\Controllers\AuthController;

$controller = new AuthController();
$controller->logout();


