<?php
declare(strict_types=1);

namespace Nexera\Models;

class LeaveRequest extends BaseModel
{
    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM leaves WHERE student_id = :student_id ORDER BY request_date DESC');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO leaves (student_id, request_date, from_date, to_date, reason, status)
                VALUES (:student_id, NOW(), :from_date, :to_date, :reason, :status)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':student_id' => $data['student_id'],
            ':from_date'  => $data['from_date'],
            ':to_date'    => $data['to_date'],
            ':reason'     => $data['reason'],
            ':status'     => $data['status'] ?? 'pending',
        ]);
    }

    public function updateStatus(int $leaveId, string $status, int $staffUserId): bool
    {
        $stmt = $this->db->prepare('UPDATE leaves SET status = :status, approved_by = :staffUserId WHERE id = :id');
        return $stmt->execute([
            ':status'      => $status,
            ':staffUserId' => $staffUserId,
            ':id'          => $leaveId,
        ]);
    }

    public function getByStudentList(array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $sql = "SELECT l.*, s.roll_number, s.first_name, s.last_name 
                FROM leaves l 
                INNER JOIN students s ON l.student_id = s.id 
                WHERE l.student_id IN ({$placeholders}) 
                ORDER BY l.request_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($studentIds);
        return $stmt->fetchAll();
    }
}


