<<<<<<< HEAD
<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/ParentController.php';

use Nexera\Config;
use Nexera\Controllers\ParentController;

require_login(Config\ROLE_PARENT);
enforce_session_timeout();

$controller = new ParentController();
$data = $controller->getDashboardData((int) $_SESSION['user']['id']);

$flash = ['success' => null, 'error' => null];
if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

$pageTitle = 'Parent Dashboard';
$extraScriptsBody = ['js/dashboard.js'];
include __DIR__ . '/views/layout/header.php';
?>

<main class="dashboard">
    <header class="dashboard__header">
        <h1>Hello, <?= e($data['parent']['father_name'] ?? $_SESSION['user']['username']); ?></h1>
        <span class="pill">Parent Portal</span>
    </header>

    <?php include __DIR__ . '/views/components/alerts.php'; ?>
    <?php if (!empty($data['message'])): ?>
        <div class="alert alert-error" role="alert">
            <?= e($data['message']); ?>
        </div>
    <?php endif; ?>

    <section class="dashboard__grid">
        <article class="glass-card">
            <h2>Parent Profile</h2>
            <p><strong>Parent Of:</strong> <?= e($data['parent']['parent_of'] ?? '-'); ?></p>
            <p><strong>Date of Birth:</strong> <?= !empty($data['parent']['dob']) ? e((new DateTime($data['parent']['dob']))->format('d M Y')) : '-'; ?></p>
            <p><strong>Father:</strong> <?= e($data['parent']['father_name'] ?? '-'); ?></p>
            <p><strong>Mother:</strong> <?= e($data['parent']['mother_name'] ?? '-'); ?></p>
            <p><strong>Contact:</strong> <?= e($data['parent']['phone'] ?? '-'); ?></p>
            <p><strong>Address:</strong> <?= e($data['parent']['address'] ?? '-'); ?></p>
        </article>

        <?php if (!empty($data['student'])): ?>
        <article class="glass-card">
            <h2>Student Overview</h2>
            <p><strong>Name:</strong> <?= e(($data['student']['first_name'] ?? '') . ' ' . ($data['student']['last_name'] ?? '')); ?></p>
            <p><strong>Roll No:</strong> <?= e($data['student']['roll_number'] ?? '-'); ?></p>
            <p><strong>Course:</strong> <?= e($data['student']['course'] ?? '-'); ?></p>
            <p><strong>Branch:</strong> <?= e($data['student']['branch'] ?? '-'); ?></p>
            <p><strong>Phone:</strong> <?= e($data['student']['phone'] ?? '-'); ?></p>
        </article>

        <article class="glass-card">
            <h2>Attendance Summary</h2>
            <div class="chart-container">
                <canvas id="attendanceChart"
                        data-present="<?= e((string) ($data['attendanceSummary']['present'] ?? 0)); ?>"
                        data-absent="<?= e((string) ($data['attendanceSummary']['absent'] ?? 0)); ?>"
                        data-on-duty="<?= e((string) ($data['attendanceSummary']['on_duty'] ?? 0)); ?>"
                        data-leave="<?= e((string) ($data['attendanceSummary']['leave'] ?? 0)); ?>">
                </canvas>
            </div>
            <p>The visual snapshot highlights attendance patterns for your child.</p>
        </article>

        <article class="glass-card">
            <h2>Recent Attendance</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['attendance'] ?? [], 0, 5) as $attendance): ?>
                        <tr>
                            <td><?= e($attendance['date']); ?></td>
                            <td><?= e($attendance['status']); ?></td>
                            <td><?= e($attendance['remarks'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Internal Marks & Feedback</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Max</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['internals'] ?? [], 0, 6) as $internal): ?>
                        <tr>
                            <td><?= e($internal['subject']); ?></td>
                            <td><?= e((string) $internal['marks']); ?></td>
                            <td><?= e((string) $internal['max_marks']); ?></td>
                            <td><?= e($internal['feedback'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Leave & On-Duty Requests</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['leaves'] ?? [], 0, 6) as $leave): ?>
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
            <h2>Placement Status</h2>
            <ul>
                <?php foreach ($data['placements'] ?? [] as $placement): ?>
                <li>
                    <strong><?= e($placement['company']); ?></strong> – <?= e($placement['position']); ?>
                    <span class="status-chip status-chip--<?= e($placement['status']); ?>">
                        <?= e($placement['status']); ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Student Location Tracking</h2>
            <p>Monitor your child's safety with upcoming location services.</p>
            <button class="neon-button tooltip" data-open-development data-tooltip="Feature in development">
                View Tracker
            </button>
        </article>
        <?php else: ?>
        <article class="glass-card">
            <h2>Link a Student</h2>
            <p>Your account is not yet connected to a student profile. Please contact the college administration or ensure that the non-teaching staff linked your profile during registration.</p>
        </article>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


=======
<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/ParentController.php';

use Nexera\Config;
use Nexera\Controllers\ParentController;

require_login(Config\ROLE_PARENT);
enforce_session_timeout();

$controller = new ParentController();
$data = $controller->getDashboardData((int) $_SESSION['user']['id']);

$flash = ['success' => null, 'error' => null];
if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

$pageTitle = 'Parent Dashboard';
$extraScriptsBody = ['js/dashboard.js'];
include __DIR__ . '/views/layout/header.php';
?>

<main class="dashboard">
    <header class="dashboard__header">
        <h1>Hello, <?= e($data['parent']['father_name'] ?? $_SESSION['user']['username']); ?></h1>
        <span class="pill">Parent Portal</span>
    </header>

    <?php include __DIR__ . '/views/components/alerts.php'; ?>
    <?php if (!empty($data['message'])): ?>
        <div class="alert alert-error" role="alert">
            <?= e($data['message']); ?>
        </div>
    <?php endif; ?>

    <section class="dashboard__grid">
        <article class="glass-card">
            <h2>Parent Profile</h2>
            <p><strong>Parent Of:</strong> <?= e($data['parent']['parent_of'] ?? '-'); ?></p>
            <p><strong>Date of Birth:</strong> <?= !empty($data['parent']['dob']) ? e((new DateTime($data['parent']['dob']))->format('d M Y')) : '-'; ?></p>
            <p><strong>Father:</strong> <?= e($data['parent']['father_name'] ?? '-'); ?></p>
            <p><strong>Mother:</strong> <?= e($data['parent']['mother_name'] ?? '-'); ?></p>
            <p><strong>Contact:</strong> <?= e($data['parent']['phone'] ?? '-'); ?></p>
            <p><strong>Address:</strong> <?= e($data['parent']['address'] ?? '-'); ?></p>
        </article>

        <?php if (!empty($data['student'])): ?>
        <article class="glass-card">
            <h2>Student Overview</h2>
            <p><strong>Name:</strong> <?= e(($data['student']['first_name'] ?? '') . ' ' . ($data['student']['last_name'] ?? '')); ?></p>
            <p><strong>Roll No:</strong> <?= e($data['student']['roll_number'] ?? '-'); ?></p>
            <p><strong>Course:</strong> <?= e($data['student']['course'] ?? '-'); ?></p>
            <p><strong>Branch:</strong> <?= e($data['student']['branch'] ?? '-'); ?></p>
            <p><strong>Phone:</strong> <?= e($data['student']['phone'] ?? '-'); ?></p>
        </article>

        <article class="glass-card">
            <h2>Attendance Summary</h2>
            <div class="chart-container">
                <canvas id="attendanceChart"
                        data-present="<?= e((string) ($data['attendanceSummary']['present'] ?? 0)); ?>"
                        data-absent="<?= e((string) ($data['attendanceSummary']['absent'] ?? 0)); ?>"
                        data-on-duty="<?= e((string) ($data['attendanceSummary']['on_duty'] ?? 0)); ?>"
                        data-leave="<?= e((string) ($data['attendanceSummary']['leave'] ?? 0)); ?>">
                </canvas>
            </div>
            <p>The visual snapshot highlights attendance patterns for your child.</p>
        </article>

        <article class="glass-card">
            <h2>Recent Attendance</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['attendance'] ?? [], 0, 5) as $attendance): ?>
                        <tr>
                            <td><?= e($attendance['date']); ?></td>
                            <td><?= e($attendance['status']); ?></td>
                            <td><?= e($attendance['remarks'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Internal Marks & Feedback</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Max</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['internals'] ?? [], 0, 6) as $internal): ?>
                        <tr>
                            <td><?= e($internal['subject']); ?></td>
                            <td><?= e((string) $internal['marks']); ?></td>
                            <td><?= e((string) $internal['max_marks']); ?></td>
                            <td><?= e($internal['feedback'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Leave & On-Duty Requests</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['leaves'] ?? [], 0, 6) as $leave): ?>
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
            <h2>Placement Status</h2>
            <ul>
                <?php foreach ($data['placements'] ?? [] as $placement): ?>
                <li>
                    <strong><?= e($placement['company']); ?></strong> – <?= e($placement['position']); ?>
                    <span class="status-chip status-chip--<?= e($placement['status']); ?>">
                        <?= e($placement['status']); ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Student Location Tracking</h2>
            <p>Monitor your child's safety with upcoming location services.</p>
            <button class="neon-button tooltip" data-open-development data-tooltip="Feature in development">
                View Tracker
            </button>
        </article>
        <?php else: ?>
        <article class="glass-card">
            <h2>Link a Student</h2>
            <p>Your account is not yet connected to a student profile. Please contact the college administration or ensure that the non-teaching staff linked your profile during registration.</p>
        </article>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


>>>>>>> 703b86c6e5612ab3a8c616b821cd2ca2d7ee0f31
