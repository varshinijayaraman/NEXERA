<?php
declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/controllers/AuthController.php';

use Nexera\Controllers\AuthController;
use Nexera\Config;

$flash = ['success' => null, 'error' => null];

if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security validation failed. Please retry.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? '');

        if ($username === '' || $password === '' || $role === '') {
            $flash['error'] = 'All fields are required.';
        } else {
            $auth = new AuthController();
            $result = $auth->login($username, $password, $role === 'staff_teaching' || $role === 'staff_non_teaching' ? Config\ROLE_STAFF : $role);

            if ($result['success'] ?? false) {
                // Check if password change is required
                if (!empty($_SESSION['force_password_change']) && $_SESSION['force_password_change'] === true) {
                    redirect('change_password.php');
                } else {
                    $destination = match ($role) {
                        'student'        => 'dashboard_student.php',
                        'parent'         => 'dashboard_parent.php',
                        'staff_teaching' => 'dashboard_staff_teaching.php',
                        'staff_non_teaching' => 'dashboard_staff_non_teaching.php',
                        default          => 'index.php',
                    };
                    redirect($destination);
                }
            } else {
                $flash['error'] = $result['message'] ?? 'Login failed.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(Config\APP_NAME); ?> &mdash; Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar" aria-label="Primary Navigation">
        <div class="navbar-brand"><?= e(Config\APP_NAME); ?></div>
        <div>
            <a class="neon-button" href="index.php">Back Home</a>
        </div>
    </nav>

    <main class="login-wrapper">
        <section class="glass-card form-card" aria-labelledby="loginHeading">
            <img src="assets/hero-glass.gif" alt="" aria-hidden="true">
            <h1 id="loginHeading">Welcome to <?= e(Config\APP_NAME); ?></h1>
            <p>Choose your role to continue.</p>

            <?php include __DIR__ . '/views/components/alerts.php'; ?>

            <div data-role-wrapper="primary">
                <div class="role-selector" role="tablist" aria-label="Role selection">
                    <button type="button" data-role-select="student" class="is-active" role="tab" aria-selected="true">Student</button>
                    <button type="button" data-role-select="parent" role="tab" aria-selected="false">Parent</button>
                    <button type="button" data-role-select="staff" role="tab" aria-selected="false">Staff</button>
                </div>

                <div data-role-panel="student">
                    <form method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="role" value="student">
                        <div class="form-group">
                            <label for="studentUsername">Student Username</label>
                            <input id="studentUsername" name="username" class="form-control" required autocomplete="username">
                        </div>
                        <div class="form-group">
                            <label for="studentPassword">Password</label>
                            <input id="studentPassword" type="password" name="password" class="form-control" required autocomplete="current-password">
                        </div>
                        <button class="neon-button" type="submit">Login</button>
                    </form>
                </div>

                <div data-role-panel="parent" hidden>
                    <form method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="role" value="parent">
                        <div class="form-group">
                            <label for="parentUsername">Parent Username</label>
                            <input id="parentUsername" name="username" class="form-control" required autocomplete="username">
                        </div>
                        <div class="form-group">
                            <label for="parentPassword">Password</label>
                            <input id="parentPassword" type="password" name="password" class="form-control" required autocomplete="current-password">
                        </div>
                        <button class="neon-button" type="submit">Login</button>
                    </form>
                </div>

                <div data-role-panel="staff" hidden>
                    <div class="glass-card" style="padding: 1.2rem; gap: 0.8rem;" data-role-wrapper="staff">
                        <h2>Staff Portal</h2>
                        <div class="role-selector" aria-label="Staff type">
                            <button type="button" data-role-select="staff_teaching">Teaching Staff</button>
                            <button type="button" data-role-select="staff_non_teaching">Non-Teaching Staff</button>
                        </div>
                        <div data-role-panel="staff_teaching" hidden>
                            <form method="POST" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="role" value="staff_teaching">
                                <div class="form-group">
                                    <label for="teachUsername">Staff Username</label>
                                    <input id="teachUsername" name="username" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="teachPassword">Password</label>
                                    <input id="teachPassword" type="password" name="password" class="form-control" required>
                                </div>
                                <button class="neon-button" type="submit">Login</button>
                            </form>
                        </div>
                        <div data-role-panel="staff_non_teaching" hidden>
                            <form method="POST" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="role" value="staff_non_teaching">
                                <div class="form-group">
                                    <label for="nonTeachUsername">Staff Username</label>
                                    <input id="nonTeachUsername" name="username" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="nonTeachPassword">Password</label>
                                    <input id="nonTeachPassword" type="password" name="password" class="form-control" required>
                                </div>
                                <button class="neon-button" type="submit">Login</button>
                            </form>
                            <p class="section__subtitle" style="font-size:0.85rem; margin-top:1rem;">
                                After login, you can register new students, parents, and teaching staff from your dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="modal-non-teaching-info" class="modal" role="dialog" aria-modal="true" aria-label="Non-teaching information">
        <div class="modal__content glass-card">
            <h2>Non-Teaching Staff Registration</h2>
            <p>Please login with authorised credentials to register students or parents.</p>
            <p>The registration portal guides you through student/parent profile creation with secure defaults.</p>
            <button type="button" class="neon-button" data-close-modal>Close</button>
        </div>
    </div>

    <script type="module" src="js/login.js"></script>
</body>
</html>


