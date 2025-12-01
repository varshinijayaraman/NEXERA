<?php
declare(strict_types=1);

namespace Nexera\Models;

class Staff extends BaseModel
{
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM staff WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);

        $staff = $stmt->fetch();
        return $staff ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO staff (user_id, name, position, teaching, qualifications, schedule_json)
                VALUES (:user_id, :name, :position, :teaching, :qualifications, :schedule_json)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':name'           => $data['name'],
            ':position'       => $data['position'],
            ':teaching'       => $data['teaching'],
            ':qualifications' => $data['qualifications'] ?? null,
            ':schedule_json'  => $data['schedule_json'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $sql = 'UPDATE staff SET name = :name, position = :position, qualifications = :qualifications,
                schedule_json = :schedule_json WHERE user_id = :user_id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name'           => $data['name'],
            ':position'       => $data['position'],
            ':qualifications' => $data['qualifications'] ?? null,
            ':schedule_json'  => json_encode($data['schedule'] ?? []),
            ':user_id'        => $userId,
        ]);
    }
}


