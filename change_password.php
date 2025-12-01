<<<<<<< HEAD
<?php
declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/User.php';

use Nexera\Models\User;
use Nexera\Config;

require_login();

$flash = ['success' => null, 'error' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security token mismatch. Try again.';
    } else {
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($newPassword === '' || $confirmPassword === '') {
            $flash['error'] = 'Please enter and confirm the new password.';
        } elseif ($newPassword !== $confirmPassword) {
            $flash['error'] = 'New password and confirmation do not match.';
        } else {
            $userModel = new User();
            $user = $userModel->findById((int) $_SESSION['user']['id']);
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                $flash['error'] = 'Current password is incorrect.';
            } else {
                $userModel->updatePassword((int) $_SESSION['user']['id'], password_hash($newPassword, PASSWORD_DEFAULT));
                $_SESSION['force_password_change'] = false;
                $_SESSION['flash']['success'] = 'Password updated successfully. Continue exploring NEXERA.';
                $role = $_SESSION['user']['role'] ?? '';
                $staffType = $_SESSION['user']['staff_type'] ?? null;
                
                $destination = match ($role) {
                    'student' => 'dashboard_student.php',
                    'parent'  => 'dashboard_parent.php',
                    'staff'   => ($staffType === Config\STAFF_NON_TEACHING) 
                                 ? 'dashboard_staff_non_teaching.php' 
                                 : 'dashboard_staff_teaching.php',
                    default   => 'index.php',
                };
                redirect($destination);
            }
        }
    }
}

$pageTitle = 'Change Password';
include __DIR__ . '/views/layout/header.php';
?>

<main class="login-wrapper">
    <section class="glass-card form-card">
        <h1>Update Password</h1>
        <p>This step keeps your account secure. Please choose a strong password.</p>
        <?php include __DIR__ . '/views/components/alerts.php'; ?>
        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input id="currentPassword" type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input id="newPassword" type="password" name="new_password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input id="confirmPassword" type="password" name="confirm_password" class="form-control" required minlength="8">
            </div>
            <button class="neon-button" type="submit">Save Password</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


=======
<?php
declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/User.php';

use Nexera\Models\User;
use Nexera\Config;

require_login();

$flash = ['success' => null, 'error' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security token mismatch. Try again.';
    } else {
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($newPassword === '' || $confirmPassword === '') {
            $flash['error'] = 'Please enter and confirm the new password.';
        } elseif ($newPassword !== $confirmPassword) {
            $flash['error'] = 'New password and confirmation do not match.';
        } else {
            $userModel = new User();
            $user = $userModel->findById((int) $_SESSION['user']['id']);
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                $flash['error'] = 'Current password is incorrect.';
            } else {
                $userModel->updatePassword((int) $_SESSION['user']['id'], password_hash($newPassword, PASSWORD_DEFAULT));
                $_SESSION['force_password_change'] = false;
                $_SESSION['flash']['success'] = 'Password updated successfully. Continue exploring NEXERA.';
                $role = $_SESSION['user']['role'] ?? '';
                $staffType = $_SESSION['user']['staff_type'] ?? null;
                
                $destination = match ($role) {
                    'student' => 'dashboard_student.php',
                    'parent'  => 'dashboard_parent.php',
                    'staff'   => ($staffType === Config\STAFF_NON_TEACHING) 
                                 ? 'dashboard_staff_non_teaching.php' 
                                 : 'dashboard_staff_teaching.php',
                    default   => 'index.php',
                };
                redirect($destination);
            }
        }
    }
}

$pageTitle = 'Change Password';
include __DIR__ . '/views/layout/header.php';
?>

<main class="login-wrapper">
    <section class="glass-card form-card">
        <h1>Update Password</h1>
        <p>This step keeps your account secure. Please choose a strong password.</p>
        <?php include __DIR__ . '/views/components/alerts.php'; ?>
        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input id="currentPassword" type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input id="newPassword" type="password" name="new_password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input id="confirmPassword" type="password" name="confirm_password" class="form-control" required minlength="8">
            </div>
            <button class="neon-button" type="submit">Save Password</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


>>>>>>> 703b86c6e5612ab3a8c616b821cd2ca2d7ee0f31
