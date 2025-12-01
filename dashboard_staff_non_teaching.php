<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/controllers/StaffController.php';
require_once __DIR__ . '/controllers/AuthController.php';

use Nexera\Config;
use Nexera\Controllers\StaffController;
use Nexera\Controllers\AuthController;

require_login(Config\ROLE_STAFF);
enforce_session_timeout();

if (($_SESSION['user']['staff_type'] ?? Config\STAFF_TEACHING) !== Config\STAFF_NON_TEACHING) {
    redirect('dashboard_staff_teaching.php');
}

$staffController = new StaffController();
$authController = new AuthController();

$data = $staffController->getDashboardData((int) $_SESSION['user']['id']);

$flash = ['success' => null, 'error' => null];
if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security token mismatch.';
    } else {
        if (isset($_POST['register_student'])) {
            $result = $authController->registerStudent([
                'username'        => trim($_POST['roll_number']),
                'password'        => trim($_POST['dob']),
                'roll_number'     => trim($_POST['roll_number']),
                'first_name'      => trim($_POST['first_name']),
                'last_name'       => trim($_POST['last_name']),
                'dob'             => trim($_POST['dob']),
                'address'         => trim($_POST['address']),
                'phone'           => trim($_POST['phone']),
                'father_name'     => trim($_POST['father_name']),
                'mother_name'     => trim($_POST['mother_name']),
                'parent_user_id'  => null,
                'hosteller'       => trim($_POST['hosteller']),
                'course'          => trim($_POST['course']),
                'branch'          => trim($_POST['branch']),
                'year_of_study'   => trim($_POST['year_of_study']),
            ]);
            $flash[$result['success'] ? 'success' : 'error'] = $result['message'];
        }

        if (isset($_POST['register_parent'])) {
            $result = $authController->registerParent([
                'username'    => trim($_POST['parent_username']),
                'password'    => trim($_POST['parent_password']),
                'parent_of'   => trim($_POST['parent_of']),
                'dob'         => $_POST['parent_dob'] ?: null,
                'father_name' => trim($_POST['parent_father_name']),
                'mother_name' => trim($_POST['parent_mother_name']),
                'phone'       => trim($_POST['parent_phone']),
                'address'     => trim($_POST['parent_address']),
                'student_roll_number' => trim($_POST['linked_student_roll']),
            ]);
            $flash[$result['success'] ? 'success' : 'error'] = $result['message'];
        }

        if (isset($_POST['register_teaching_staff'])) {
            $result = $authController->registerTeachingStaff([
                'username'      => trim($_POST['staff_username']),
                'password'      => trim($_POST['staff_password']),
                'name'          => trim($_POST['staff_name']),
                'position'      => trim($_POST['staff_position']),
                'qualifications' => trim($_POST['staff_qualifications']),
                'schedule_json' => null,
            ]);
            $flash[$result['success'] ? 'success' : 'error'] = $result['message'];
        }
    }

    $data = $staffController->getDashboardData((int) $_SESSION['user']['id']);
}

$pageTitle = 'Non-Teaching Staff Dashboard';
$extraScriptsBody = ['js/dashboard.js'];
include __DIR__ . '/views/layout/header.php';
?>

<main class="dashboard">
    <header class="dashboard__header">
        <h1>Non-Teaching Staff Operations</h1>
        <span class="pill">Administration</span>
    </header>

    <?php include __DIR__ . '/views/components/alerts.php'; ?>

    <section class="dashboard__grid">
        <article class="glass-card">
            <h2>Register Student</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="register_student" value="1">
                <div class="form-group">
                    <label for="rollNumber">Roll Number</label>
                    <input id="rollNumber" name="roll_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input id="firstName" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input id="lastName" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input id="dob" type="date" name="dob" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="fatherName">Father Name</label>
                    <input id="fatherName" name="father_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="motherName">Mother Name</label>
                    <input id="motherName" name="mother_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="hosteller">Hosteller/Day Scholar</label>
                    <select id="hosteller" name="hosteller" class="form-control" required>
                        <option value="hosteller">Hosteller</option>
                        <option value="day_scholar">Day Scholar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="course">Course</label>
                    <input id="course" name="course" class="form-control">
                </div>
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <input id="branch" name="branch" class="form-control">
                </div>
                <div class="form-group">
                    <label for="yearOfStudy">Year of Study</label>
                    <input id="yearOfStudy" name="year_of_study" class="form-control">
                </div>
                <button class="neon-button" type="submit">Register Student</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Register Parent/Guardian</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="register_parent" value="1">
                <div class="form-group">
                    <label for="parentUsername">Username</label>
                    <input id="parentUsername" name="parent_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="parentPassword">Initial Password</label>
                    <input id="parentPassword" type="password" name="parent_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="parentOf">Parent Of</label>
                    <input id="parentOf" name="parent_of" class="form-control" placeholder="Parent of Student Name" required>
                </div>
                <div class="form-group">
                    <label for="parentDob">Parent Date of Birth</label>
                    <input id="parentDob" type="date" name="parent_dob" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parentFatherName">Father Name</label>
                    <input id="parentFatherName" name="parent_father_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parentMotherName">Mother Name</label>
                    <input id="parentMotherName" name="parent_mother_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parentPhone">Phone</label>
                    <input id="parentPhone" name="parent_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parentAddress">Address</label>
                    <textarea id="parentAddress" name="parent_address" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="linkedStudentRoll">Linked Student Roll No</label>
                    <input id="linkedStudentRoll" name="linked_student_roll" class="form-control" required>
                </div>
                <button class="neon-button" type="submit">Register Parent</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Register Teaching Staff</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="register_teaching_staff" value="1">
                <div class="form-group">
                    <label for="staffUsername">Username</label>
                    <input id="staffUsername" name="staff_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="staffPassword">Password</label>
                    <input id="staffPassword" type="password" name="staff_password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="staffName">Full Name</label>
                    <input id="staffName" name="staff_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="staffPosition">Position</label>
                    <input id="staffPosition" name="staff_position" class="form-control" placeholder="e.g., Associate Professor - CSE" required>
                </div>
                <div class="form-group">
                    <label for="staffQualifications">Qualifications</label>
                    <input id="staffQualifications" name="staff_qualifications" class="form-control" placeholder="e.g., Ph.D. in Computer Science">
                </div>
                <button class="neon-button" type="submit">Register Teaching Staff</button>
            </form>
        </article>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


