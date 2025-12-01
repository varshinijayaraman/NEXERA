<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/controllers/StaffController.php';

use Nexera\Config;
use Nexera\Controllers\StaffController;

require_login(Config\ROLE_STAFF);
enforce_session_timeout();

if (($_SESSION['user']['staff_type'] ?? Config\STAFF_NON_TEACHING) !== Config\STAFF_TEACHING) {
    redirect('dashboard_staff_non_teaching.php');
}

$controller = new StaffController();
$data = $controller->getDashboardData((int) $_SESSION['user']['id']);

$flash = ['success' => null, 'error' => null];
if (!empty($_SESSION['flash'])) {
    $flash = array_merge($flash, $_SESSION['flash']);
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $flash['error'] = 'Security token mismatch.';
    } else {
        if (isset($_POST['record_attendance'])) {
            $created = $controller->recordAttendance([
                'student_id' => (int) $_POST['student_id'],
                'date'       => $_POST['date'],
                'status'     => $_POST['status'],
                'remarks'    => trim($_POST['remarks']),
            ]);
            $flash[$created ? 'success' : 'error'] = $created ? 'Attendance saved.' : 'Unable to save attendance.';
        }

        if (isset($_POST['update_internal'])) {
            $updated = $controller->upsertInternal([
                'student_id' => (int) $_POST['student_id'],
                'subject'    => trim($_POST['subject']),
                'marks'      => (int) $_POST['marks'],
                'max_marks'  => (int) $_POST['max_marks'],
                'term'       => trim($_POST['term']),
                'feedback'   => trim($_POST['feedback']),
            ]);
            $flash[$updated ? 'success' : 'error'] = $updated ? 'Internal marks updated.' : 'Unable to update marks.';
        }

        if (isset($_POST['assign_task'])) {
            $assigned = $controller->assignTask([
                'assigned_by'        => (int) $_SESSION['user']['id'],
                'assigned_to_user_id'=> (int) $_POST['assigned_to_user_id'],
                'title'              => trim($_POST['title']),
                'description'        => trim($_POST['description']),
                'due_date'           => $_POST['due_date'],
                'status'             => 'assigned',
            ]);
            $flash[$assigned ? 'success' : 'error'] = $assigned ? 'Task assigned successfully.' : 'Could not assign task.';
        }

        if (isset($_POST['approve_leave'])) {
            $updated = $controller->approveLeave((int) $_POST['leave_id'], $_POST['approve_leave'], (int) $_SESSION['user']['id']);
            $flash[$updated ? 'success' : 'error'] = $updated ? 'Leave status updated.' : 'Unable to update leave request.';
        }
    }

    $data = $controller->getDashboardData((int) $_SESSION['user']['id']);
}

$leaveSummary = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
foreach ($data['recentLeaves'] as $leave) {
    $status = $leave['status'] ?? 'pending';
    if (isset($leaveSummary[$status])) {
        $leaveSummary[$status]++;
    }
}

$pageTitle = 'Teaching Staff Dashboard';
$extraScriptsBody = ['js/dashboard.js'];
include __DIR__ . '/views/layout/header.php';
?>

<main class="dashboard">
    <header class="dashboard__header">
        <h1>Teaching Staff Console</h1>
        <span class="pill">Academic Operations</span>
    </header>

    <?php include __DIR__ . '/views/components/alerts.php'; ?>

    <section class="dashboard__grid">
        <article class="glass-card">
            <h2>Personal Profile</h2>
            <p><strong>Name:</strong> <?= e($data['staff']['name'] ?? ''); ?></p>
            <p><strong>Position:</strong> <?= e($data['staff']['position'] ?? ''); ?></p>
            <p><strong>Qualifications:</strong> <?= e($data['staff']['qualifications'] ?? ''); ?></p>
        </article>

        <article class="glass-card">
            <h2>Leave Analytics</h2>
            <div class="chart-container">
                <canvas id="leaveChart"
                        data-approved="<?= e((string) $leaveSummary['approved']); ?>"
                        data-pending="<?= e((string) $leaveSummary['pending']); ?>"
                        data-rejected="<?= e((string) $leaveSummary['rejected']); ?>">
                </canvas>
            </div>
            <p>A quick snapshot of leave approvals helps balance academic planning.</p>
        </article>

        <article class="glass-card">
            <h2>Mark Attendance</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="record_attendance" value="1">
                <div class="form-group">
                    <label for="studentId">Student</label>
                    <select id="studentId" name="student_id" class="form-control" required>
                        <?php foreach ($data['students'] as $student): ?>
                            <option value="<?= e((string) $student['id']); ?>">
                                <?= e($student['roll_number'] . ' - ' . $student['first_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="attendanceDate">Date</label>
                    <input id="attendanceDate" type="date" name="date" class="form-control" required value="<?= date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="attendanceStatus">Status</label>
                    <select id="attendanceStatus" name="status" class="form-control" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="on_duty">On Duty</option>
                        <option value="leave">Leave</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="attendanceRemarks">Remarks</label>
                    <textarea id="attendanceRemarks" name="remarks" class="form-control" rows="2"></textarea>
                </div>
                <button class="neon-button" type="submit">Save Attendance</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Update Internal Marks</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="update_internal" value="1">
                <div class="form-group">
                    <label for="internalStudent">Student</label>
                    <select id="internalStudent" name="student_id" class="form-control" required>
                        <?php foreach ($data['students'] as $student): ?>
                            <option value="<?= e((string) $student['id']); ?>">
                                <?= e($student['roll_number'] . ' - ' . $student['first_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="marks">Marks</label>
                    <input id="marks" type="number" name="marks" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="maxMarks">Max Marks</label>
                    <input id="maxMarks" type="number" name="max_marks" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="term">Term</label>
                    <input id="term" name="term" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="feedback">Feedback</label>
                    <textarea id="feedback" name="feedback" class="form-control" rows="2"></textarea>
                </div>
                <button class="neon-button" type="submit">Update</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Student Leave Requests</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($data['recentLeaves'], 0, 6) as $leave): ?>
                        <tr>
                            <td><?= e(($leave['roll_number'] ?? '') . ' - ' . ($leave['first_name'] ?? '') . ' ' . ($leave['last_name'] ?? '')); ?></td>
                            <td><?= e($leave['from_date']); ?></td>
                            <td><?= e($leave['to_date']); ?></td>
                            <td><?= e($leave['status']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                    <input type="hidden" name="leave_id" value="<?= e((string) $leave['id']); ?>">
                                    <button class="neon-button" name="approve_leave" value="approved">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                    <input type="hidden" name="leave_id" value="<?= e((string) $leave['id']); ?>">
                                    <button class="neon-button" name="approve_leave" value="rejected">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="glass-card">
            <h2>Task Assignments</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="assign_task" value="1">
                <div class="form-group">
                    <label for="assignTo">Assign To</label>
                    <select id="assignTo" name="assigned_to_user_id" class="form-control" required>
                        <?php foreach ($data['students'] as $student): ?>
                            <option value="<?= e((string) $student['user_id']); ?>">
                                <?= e($student['first_name'] . ' ' . $student['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="taskTitle">Title</label>
                    <input id="taskTitle" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="dueDate">Due Date</label>
                    <input id="dueDate" type="date" name="due_date" class="form-control" required>
                </div>
                <button class="neon-button" type="submit">Assign Task</button>
            </form>
        </article>

        <article class="glass-card">
            <h2>Placement Updates</h2>
            <ul>
                <?php foreach ($data['recentPlacements'] as $placement): ?>
                <li><?= e($placement['company']); ?> – <?= e($placement['position']); ?> (<?= e($placement['status']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Document Vault</h2>
            <p>Upload study materials from the secure staff portal.</p>
            <ul>
                <?php foreach ($data['documents'] as $document): ?>
                <li><?= e($document['title']); ?></li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="glass-card">
            <h2>Mentorship</h2>
            <p>Assign mentors to personalised student cohorts and track engagement metrics.</p>
        </article>
    </section>
</main>

<?php include __DIR__ . '/views/layout/footer.php'; ?>


