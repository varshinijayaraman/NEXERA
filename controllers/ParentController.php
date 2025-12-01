<?php
declare(strict_types=1);

namespace Nexera\Controllers;

use Nexera\Models\ParentModel;
use Nexera\Models\Student;
use Nexera\Models\Attendance;
use Nexera\Models\Internal;
use Nexera\Models\LeaveRequest;
use Nexera\Models\Placement;

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ParentModel.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Internal.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/Placement.php';

class ParentController
{
    private ParentModel $parentModel;
    private Student $studentModel;
    private Attendance $attendanceModel;
    private Internal $internalModel;
    private LeaveRequest $leaveModel;
    private Placement $placementModel;

    public function __construct()
    {
        $this->parentModel = new ParentModel();
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
        $this->internalModel = new Internal();
        $this->leaveModel = new LeaveRequest();
        $this->placementModel = new Placement();
    }

    public function getDashboardData(int $userId): array
    {
        $parent = $this->parentModel->findByUserId($userId);

        if (!$parent) {
            return [];
        }

        $student = null;
        if (!empty($parent['linked_student_id'])) {
            $student = $this->studentModel->findById((int) $parent['linked_student_id']);
        }

        if (!$student) {
            $student = $this->studentModel->findByParentUserId($userId);
        }

        if (!$student) {
            return [
                'parent'            => $parent,
                'student'           => null,
                'attendance'        => [],
                'attendanceSummary' => ['present' => 0, 'absent' => 0, 'on_duty' => 0, 'leave' => 0],
                'internals'         => [],
                'leaves'            => [],
                'placements'        => [],
                'message'           => 'No student linked yet.',
            ];
        }

        $attendance = $this->attendanceModel->getByStudent((int) $student['id']);
        $attendanceSummary = $this->attendanceModel->analyticsSummary((int) $student['id']);
        $internals = $this->internalModel->getByStudent((int) $student['id']);
        $leaves = $this->leaveModel->getByStudent((int) $student['id']);
        $placements = $this->placementModel->getByStudent((int) $student['id']);

        return [
            'parent'            => $parent,
            'student'           => $student,
            'attendance'        => $attendance,
            'attendanceSummary' => $attendanceSummary,
            'internals'         => $internals,
            'leaves'            => $leaves,
            'placements'        => $placements,
        ];
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return $this->parentModel->updateProfile($userId, $data);
    }
}


