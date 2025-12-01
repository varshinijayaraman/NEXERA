<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/controllers/StudentController.php';

use Nexera\Config;
use Nexera\Controllers\StudentController;

require_login(Config\ROLE_STUDENT);
enforce_session_timeout();

$controller = new StudentController();
$data = $controller->getDashboardData((int) $_SESSION['user']['id']);

$flash = ['success' => null, 'error' => null];
if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security check failed.';
    } else {
        if (isset($_POST['update_profile'])) {
            $updated = $controller->updateProfile((int) $_SESSION['user']['id'], [
                'first_name'    => trim($_POST['first_name']),
                'last_name'     => trim($_POST['last_name']),
                'address'       => trim($_POST['address']),
                'phone'         => trim($_POST['phone']),
                'course'        => trim($_POST['course']),
                'branch'        => trim($_POST['branch']),
                'year_of_study' => trim($_POST['year_of_study']),
            ]);
            $flash[$updated ? 'success' : 'error'] = $updated ? 'Profile updated successfully.' : 'Unable to update profile.';
            $data = $controller->getDashboardData((int) $_SESSION['user']['id']);
        }

        if (isset($_POST['request_leave'])) {
            $submitted = $controller->requestLeave((int) $_SESSION['user']['id'], [
                'from_date' => $_POST['from_date'],
                'to_date'   => $_POST['to_date'],
                'reason'    => trim($_POST['reason']),
            ]);
            $flash[$submitted ? 'success' : 'error'] = $submitted ? 'Leave request submitted.' : 'Could not submit leave request.';
            $data = $controller->getDashboardData((int) $_SESSION['user']['id']);
        }

        if (isset($_POST['complete_task'])) {
            $controller->updateTaskStatus((int) $_POST['task_id'], 'completed');
            $flash['success'] = 'Task updated. Great job!';
            $data = $controller->getDashboardData((int) $_SESSION['user']['id']);
        }
    }
}

$attendanceSummary = $data['attendanceSummary'] ?? ['present' => 0, 'absent' => 0, 'on_duty' => 0, 'leave' => 0];

$pageTitle = 'Student Dashboard';
$extraScriptsBody = ['js/dashboard.js'];
include __DIR__ . '/views/layout/header.php';
?>
<main class="dashboard">
    <header class="dashboard__header">
        <h1>Welcome back, <?= e($data['student']['first_name'] ?? $_SESSION['user']['username']); ?></h1>
        <span class="pill">Student Portal</span>
    </header>

    <?php include __DIR__ . '/views/components/alerts.php'; ?>

    <section class="dashboard__grid">
        <article class="glass-card">
            <h2>Profile Overview</h2>
            <form method="POST" class="profile-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input id="firstName" name="first_name" class="form-control" value="<?= e($data['student']['first_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input id="lastName" name="last_name" class="form-control" value="<?= e($data['student']['last_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="2"><?= e($data['student']['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" class="form-control" value="<?= e($data['student']['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="course">Course</label>
                    <input id="course" name="course" class="form-control" value="<?= e($data['student']['course'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <input id="branch" name="branch" class="form-control" value="<?= e($data['student']['branch'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="year">Year of Study</label>
                    <input id="year" name="year_of_study" class="form-control" value="<?= e($data['student']['year_of_study'] ?? ''); ?>">
                </div>
                <button class="neon-button" type="submit">Save Changes</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Attendance Analytics</h2>
            <div class="chart-container">
                <canvas id="attendanceChart"
                        data-present="<?= e((string) $attendanceSummary['present']); ?>"
                        data-absent="<?= e((string) $attendanceSummary['absent']); ?>"
                        data-on-duty="<?= e((string) $attendanceSummary['on_duty']); ?>"
                        data-leave="<?= e((string) $attendanceSummary['leave']); ?>">
                </canvas>
            </div>
            <div class="table-responsive">
                <table aria-label="Recent attendance records">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['attendance'] ?? [], 0, 6) as $attendance): ?>
                            <tr>
                                <td><?= e((new DateTime($attendance['date']))->format('d M Y')); ?></td>
                                <td><?= e($attendance['status']); ?></td>
                                <td><?= e($attendance['remarks'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Leave & On-Duty</h2>
            <form method="POST" class="form-card" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="request_leave" value="1">
                <div class="form-group">
                    <label for="fromDate">From</label>
                    <input id="fromDate" type="date" name="from_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="toDate">To</label>
                    <input id="toDate" type="date" name="to_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea id="reason" name="reason" class="form-control" rows="2" required></textarea>
                </div>
                <button class="neon-button" type="submit">Submit Request</button>
            </form>

            <div class="table-responsive" style="margin-top:1rem;">
                <table aria-label="Leave history">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['leaves'] ?? [], 0, 5) as $leave): ?>
                            <tr>
                                <td><?= e($leave['from_date']); ?></td>
                                <td><?= e($leave['to_date']); ?></td>
                                <td>
                                    <span class="status-chip status-chip--<?= e($leave['status']); ?>">
                                        <?= e($leave['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Internal Assessments</h2>
            <div class="table-responsive">
                <table aria-label="Internal marks">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Max</th>
                            <th>Term</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['internals'] ?? [], 0, 6) as $internal): ?>
                            <tr>
                                <td><?= e($internal['subject']); ?></td>
                                <td><?= e((string) $internal['marks']); ?></td>
                                <td><?= e((string) $internal['max_marks']); ?></td>
                                <td><?= e($internal['term']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Tasks & Assignments</h2>
            <?php foreach ($data['tasks'] ?? [] as $task): ?>
                <div class="glass-card" style="padding:1rem; margin-bottom:1rem;">
                    <h3><?= e($task['title']); ?></h3>
                    <p><?= e($task['description']); ?></p>
                    <p><strong>Due:</strong> <?= e($task['due_date']); ?></p>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="task_id" value="<?= e((string) $task['id']); ?>">
                        <button class="neon-button" type="submit" name="complete_task" value="1">
                            Mark as Done
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </article>

        <article class="glass-card">
            <h2>Placements & Opportunities</h2>
            <ul>
                <?php foreach ($data['placements'] ?? [] as $placement): ?>
                    <li>
                        <strong><?= e($placement['company']); ?></strong>
                        – <?= e($placement['position']); ?>
                        <span class="status-chip status-chip--<?= e($placement['status']); ?>">
                            <?= e($placement['status']); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Digital Resources</h2>
            <ul>
                <?php foreach ($data['documents'] ?? [] as $doc): ?>
                    <li>
                        <a href="secure_download.php?id=<?= e((string) $doc['id']); ?>">
                            <?= e($doc['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>College Events & Communication</h2>
            <p>Stay tuned for upcoming seminars, alumni meets, and placement drives.</p>
            <ul>
                <li>Soft Skills Workshop – Friday, 3 PM</li>
                <li>Placement Prep Bootcamp – Next Tuesday</li>
                <li>Innovation Hackathon – Submissions open</li>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Fee Status</h2>
            <div class="table-responsive">
                <table aria-label="Fee summary">
                    <thead>
                        <tr>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['fees'] ?? [] as $fee): ?>
                            <tr>
                                <td><?= e(number_format((float) $fee['amount_due'], 2)); ?></td>
                                <td><?= e(number_format((float) $fee['amount_paid'], 2)); ?></td>
                                <td><?= e((new DateTime($fee['due_date']))->format('d M Y')); ?></td>
                                <td><?= e($fee['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Student Location Tracking</h2>
            <p>This feature enhances campus safety and communication.</p>
            <button class="neon-button tooltip" data-open-development data-tooltip="Feature in development">
                Launch Tracker
            </button>
        </article>
    </section>
</main>
<?php include __DIR__ . '/views/layout/footer.php'; ?>


