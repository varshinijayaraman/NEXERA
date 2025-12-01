<?php
declare(strict_types=1);

namespace Nexera\Models;

class Student extends BaseModel
{
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $student = $stmt->fetch();

        return $student ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        return $student ?: null;
    }

    public function findByParentUserId(int $parentUserId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE parent_user_id = :parent_user_id LIMIT 1');
        $stmt->execute([':parent_user_id' => $parentUserId]);
        $student = $stmt->fetch();

        return $student ?: null;
    }

    public function findByRollNumber(string $rollNumber): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE roll_number = :roll LIMIT 1');
        $stmt->execute([':roll' => $rollNumber]);
        $student = $stmt->fetch();

        return $student ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO students (
            user_id, roll_number, first_name, last_name, dob, address, phone,
            father_name, mother_name, parent_user_id, hosteller, course, branch, year_of_study
        ) VALUES (
            :user_id, :roll_number, :first_name, :last_name, :dob, :address, :phone,
            :father_name, :mother_name, :parent_user_id, :hosteller, :course, :branch, :year_of_study
        )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':roll_number'    => $data['roll_number'],
            ':first_name'     => $data['first_name'],
            ':last_name'      => $data['last_name'],
            ':dob'            => $data['dob'],
            ':address'        => $data['address'],
            ':phone'          => $data['phone'],
            ':father_name'    => $data['father_name'],
            ':mother_name'    => $data['mother_name'],
            ':parent_user_id' => $data['parent_user_id'],
            ':hosteller'      => $data['hosteller'],
            ':course'         => $data['course'] ?? null,
            ':branch'         => $data['branch'] ?? null,
            ':year_of_study'  => $data['year_of_study'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $sql = 'UPDATE students SET first_name = :first_name, last_name = :last_name, address = :address,
                phone = :phone, course = :course, branch = :branch, year_of_study = :year
                WHERE user_id = :user_id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':address'    => $data['address'],
            ':phone'      => $data['phone'],
            ':course'     => $data['course'] ?? null,
            ':branch'     => $data['branch'] ?? null,
            ':year'       => $data['year_of_study'] ?? null,
            ':user_id'    => $userId,
        ]);
    }

    public function listRecent(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT * FROM students ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function linkParent(int $studentId, int $parentUserId): bool
    {
        $stmt = $this->db->prepare('UPDATE students SET parent_user_id = :parent_user_id WHERE id = :id');
        return $stmt->execute([
            ':parent_user_id' => $parentUserId,
            ':id'             => $studentId,
        ]);
    }
}


