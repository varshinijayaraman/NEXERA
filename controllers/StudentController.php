<?php
declare(strict_types=1);

namespace Nexera\Controllers;

use Nexera\Models\Student;
use Nexera\Models\Attendance;
use Nexera\Models\Internal;
use Nexera\Models\LeaveRequest;
use Nexera\Models\Placement;
use Nexera\Models\Task;
use Nexera\Models\Document;
use Nexera\Models\Fee;

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Internal.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/Placement.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Fee.php';
require_once __DIR__ . '/../includes/logger.php';

class StudentController
{
    private Student $studentModel;
    private Attendance $attendanceModel;
    private Internal $internalModel;
    private LeaveRequest $leaveModel;
    private Placement $placementModel;
    private Task $taskModel;
    private Document $documentModel;
    private Fee $feeModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
        $this->internalModel = new Internal();
        $this->leaveModel = new LeaveRequest();
        $this->placementModel = new Placement();
        $this->taskModel = new Task();
        $this->documentModel = new Document();
        $this->feeModel = new Fee();
    }

    public function getDashboardData(int $userId): array
    {
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return [];
        }

        $attendance = $this->attendanceModel->getByStudent((int) $student['id']);
        $attendanceSummary = $this->attendanceModel->analyticsSummary((int) $student['id']);
        $internals = $this->internalModel->getByStudent((int) $student['id']);
        $leaves = $this->leaveModel->getByStudent((int) $student['id']);
        $placements = $this->placementModel->getByStudent((int) $student['id']);
        $tasks = $this->taskModel->getAssignedToUser($userId);
        $documents = $this->documentModel->listByVisibility('student');
        $fees = $this->feeModel->getByStudent((int) $student['id']);

        return [
            'student'           => $student,
            'attendance'        => $attendance,
            'attendanceSummary' => $attendanceSummary,
            'internals'         => $internals,
            'leaves'            => $leaves,
            'placements'        => $placements,
            'tasks'             => $tasks,
            'documents'         => $documents,
            'fees'              => $fees,
        ];
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $result = $this->studentModel->updateProfile($userId, $data);

        if ($result) {
            audit_log($userId, 'student profile updated');
        }

        return $result;
    }

    public function requestLeave(int $userId, array $data): bool
    {
        $student = $this->studentModel->findByUserId($userId);
        if (!$student) {
            return false;
        }

        $result = $this->leaveModel->create([
            'student_id' => (int) $student['id'],
            'from_date'  => $data['from_date'],
            'to_date'    => $data['to_date'],
            'reason'     => $data['reason'],
        ]);

        if ($result) {
            audit_log($userId, 'submitted leave request');
        }

        return $result;
    }

    public function updateTaskStatus(int $taskId, string $status): bool
    {
        $result = $this->taskModel->updateStatus($taskId, $status);
        if ($result) {
            audit_log($_SESSION['user']['id'] ?? null, "task {$taskId} status updated to {$status}");
        }
        return $result;
    }
}


