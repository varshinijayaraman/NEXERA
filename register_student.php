<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';

if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'staff') {
    redirect('dashboard_staff_non_teaching.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXERA &mdash; Staff Registration Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-wrapper">
        <section class="glass-card form-card">
            <h1>Access Restricted</h1>
            <p>The registration portal is available for authorised non-teaching staff members.</p>
            <p>Please login with your staff credentials to onboard students and parents.</p>
            <a class="neon-button" href="login.php">Go to Login</a>
        </section>
    </main>
</body>
</html>


