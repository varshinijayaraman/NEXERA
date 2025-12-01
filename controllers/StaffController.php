<?php
declare(strict_types=1);

namespace Nexera\Controllers;

use Nexera\Models\Staff;
use Nexera\Models\Attendance;
use Nexera\Models\Student;
use Nexera\Models\Internal;
use Nexera\Models\LeaveRequest;
use Nexera\Models\Placement;
use Nexera\Models\Task;
use Nexera\Models\Document;
use Nexera\Models\Fee;

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Internal.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/Placement.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Fee.php';
require_once __DIR__ . '/../includes/logger.php';

class StaffController
{
    private Staff $staffModel;
    private Attendance $attendanceModel;
    private Student $studentModel;
    private Internal $internalModel;
    private LeaveRequest $leaveModel;
    private Placement $placementModel;
    private Task $taskModel;
    private Document $documentModel;
    private Fee $feeModel;

    public function __construct()
    {
        $this->staffModel = new Staff();
        $this->attendanceModel = new Attendance();
        $this->studentModel = new Student();
        $this->internalModel = new Internal();
        $this->leaveModel = new LeaveRequest();
        $this->placementModel = new Placement();
        $this->taskModel = new Task();
        $this->documentModel = new Document();
        $this->feeModel = new Fee();
    }

    public function getDashboardData(int $userId): array
    {
        $staff = $this->staffModel->findByUserId($userId);
        $students = $this->studentModel->listRecent(10);
        $studentIds = array_map('intval', array_column($students, 'id'));
        $recentLeaves = $this->leaveModel->getByStudentList($studentIds);
        $recentPlacements = $this->placementModel->recentOpportunities();
        $documents = $this->documentModel->listByVisibility('staff');
        $studentsWithFees = array_map(function ($student) {
            $student['fees'] = $this->feeModel->getByStudent((int) $student['id']);
            return $student;
        }, $students);

        return [
            'staff'           => $staff,
            'students'        => $studentsWithFees,
            'recentLeaves'    => $recentLeaves,
            'recentPlacements'=> $recentPlacements,
            'documents'       => $documents,
        ];
    }

    public function recordAttendance(array $data): bool
    {
        $result = $this->attendanceModel->record($data);
        if ($result) {
            audit_log($_SESSION['user']['id'] ?? null, "recorded attendance for student {$data['student_id']}");
        }
        return $result;
    }

    public function upsertInternal(array $data): bool
    {
        $result = $this->internalModel->upsert($data);
        if ($result) {
            audit_log($_SESSION['user']['id'] ?? null, "updated internals for student {$data['student_id']}");
        }
        return $result;
    }

    public function approveLeave(int $leaveId, string $status, int $staffUserId): bool
    {
        $result = $this->leaveModel->updateStatus($leaveId, $status, $staffUserId);
        if ($result) {
            audit_log($staffUserId, "leave {$leaveId} set to {$status}");
        }
        return $result;
    }

    public function assignTask(array $data): bool
    {
        $result = $this->taskModel->create($data);
        if ($result) {
            audit_log($data['assigned_by'], "task assigned to {$data['assigned_to_user_id']}");
        }
        return $result;
    }
}


