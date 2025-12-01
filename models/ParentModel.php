<?php
declare(strict_types=1);

namespace Nexera\Models;

class ParentModel extends BaseModel
{
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM parents WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);

        $parent = $stmt->fetch();
        return $parent ?: null;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $sql = 'UPDATE parents SET parent_of = :parent_of, dob = :dob, father_name = :father_name, mother_name = :mother_name,
                phone = :phone, address = :address WHERE user_id = :user_id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':parent_of'  => $data['parent_of'],
            ':dob'        => $data['dob'],
            ':father_name' => $data['father_name'],
            ':mother_name' => $data['mother_name'],
            ':phone'       => $data['phone'],
            ':address'     => $data['address'],
            ':user_id'     => $userId,
        ]);
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO parents (user_id, parent_of, dob, father_name, mother_name, phone, address) VALUES
                (:user_id, :parent_of, :dob, :father_name, :mother_name, :phone, :address)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'     => $data['user_id'],
            ':parent_of'   => $data['parent_of'],
            ':dob'         => $data['dob'],
            ':father_name' => $data['father_name'],
            ':mother_name' => $data['mother_name'],
            ':phone'       => $data['phone'],
            ':address'     => $data['address'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function linkStudent(int $parentUserId, int $studentId): bool
    {
        $stmt = $this->db->prepare('UPDATE parents SET linked_student_id = :student_id WHERE user_id = :user_id');
        return $stmt->execute([
            ':student_id' => $studentId,
            ':user_id'    => $parentUserId,
        ]);
    }
}


