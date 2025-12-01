<?php
declare(strict_types=1);

namespace Nexera\Controllers;

use Nexera\Models\User;
use Nexera\Models\Student;
use Nexera\Models\ParentModel;
use Nexera\Models\Staff;
use Nexera\Config;

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/ParentModel.php';
require_once __DIR__ . '/../models/Staff.php';

class AuthController
{
    private User $userModel;
    private Student $studentModel;
    private ParentModel $parentModel;
    private Staff $staffModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->parentModel = new ParentModel();
        $this->staffModel = new Staff();
    }

    public function login(string $username, string $password, ?string $expectedRole = null): array
    {
        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        if ($expectedRole !== null && $user['role'] !== $expectedRole) {
            return ['success' => false, 'message' => 'Role mismatch, please select the correct portal.'];
        }

        if ($user['status'] === 'inactive') {
            return ['success' => false, 'message' => 'Account inactive. Contact administrator.'];
        }

        regenerate_session();

        $_SESSION['user'] = [
            'id'       => (int) $user['id'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ];

        if ($user['role'] === Config\ROLE_STAFF) {
            $staff = $this->staffModel->findByUserId((int) $user['id']);
            $_SESSION['user']['staff_type'] = $staff && (int) ($staff['teaching'] ?? 0) === 1
                ? Config\STAFF_TEACHING
                : Config\STAFF_NON_TEACHING;
        }

        $_SESSION['force_password_change'] = ($user['status'] === 'force_reset');

        enforce_session_timeout();

        $this->userModel->recordLogin((int) $user['id']);
        audit_log((int) $user['id'], 'login');

        return ['success' => true];
    }

    public function logout(): void
    {
        audit_log($_SESSION['user']['id'] ?? null, 'logout');

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect('login.php?logged_out=1');
    }

    public function registerStudent(array $data): array
    {
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $this->userModel->create($data['username'], $passwordHash, Config\ROLE_STUDENT, 'force_reset');

        $studentId = $this->studentModel->create([
            'user_id'        => $userId,
            'roll_number'    => $data['roll_number'],
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'dob'            => $data['dob'],
            'address'        => $data['address'],
            'phone'          => $data['phone'],
            'father_name'    => $data['father_name'],
            'mother_name'    => $data['mother_name'],
            'parent_user_id' => $data['parent_user_id'] ?? null,
            'hosteller'      => $data['hosteller'],
            'course'         => $data['course'] ?? null,
            'branch'         => $data['branch'] ?? null,
            'year_of_study'  => $data['year_of_study'] ?? null,
        ]);

        audit_log($_SESSION['user']['id'] ?? null, "registered student {$studentId}");

        return ['success' => true, 'message' => 'Student registered successfully.'];
    }

    public function registerParent(array $data): array
    {
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $this->userModel->create($data['username'], $passwordHash, Config\ROLE_PARENT);

        $this->parentModel->create([
            'user_id'     => $userId,
            'parent_of'   => $data['parent_of'],
            'dob'         => $data['dob'],
            'father_name' => $data['father_name'],
            'mother_name' => $data['mother_name'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
        ]);

        if (!empty($data['student_roll_number'])) {
            $student = $this->studentModel->findByRollNumber($data['student_roll_number']);
            if ($student) {
                $this->studentModel->linkParent((int) $student['id'], $userId);
                $this->parentModel->linkStudent($userId, (int) $student['id']);
            }
        }

        audit_log($_SESSION['user']['id'] ?? null, "registered parent user {$userId}");

        return ['success' => true, 'message' => 'Parent registered successfully.'];
    }

    public function registerTeachingStaff(array $data): array
    {
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $this->userModel->create($data['username'], $passwordHash, Config\ROLE_STAFF);

        $this->staffModel->create([
            'user_id'        => $userId,
            'name'           => $data['name'],
            'position'       => $data['position'],
            'teaching'       => 1, // Teaching staff
            'qualifications' => $data['qualifications'] ?? null,
            'schedule_json'  => $data['schedule_json'] ?? null,
        ]);

        audit_log($_SESSION['user']['id'] ?? null, "registered teaching staff user {$userId}");

        return ['success' => true, 'message' => 'Teaching staff registered successfully.'];
    }
}


