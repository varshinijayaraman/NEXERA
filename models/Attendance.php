<?php
declare(strict_types=1);

namespace Nexera\Models;

class Attendance extends BaseModel
{
    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM attendance WHERE student_id = :student_id ORDER BY date DESC');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function record(array $data): bool
    {
        // Use INSERT ... ON DUPLICATE KEY UPDATE to handle existing records
        $stmt = $this->db->prepare(
            'INSERT INTO attendance (student_id, date, status, remarks)
             VALUES (:student_id, :date, :status, :remarks)
             ON DUPLICATE KEY UPDATE
             status = VALUES(status),
             remarks = VALUES(remarks)'
        );

        return $stmt->execute([
            ':student_id' => $data['student_id'],
            ':date'       => $data['date'],
            ':status'     => $data['status'],
            ':remarks'    => $data['remarks'] ?? null,
        ]);
    }

    public function analyticsSummary(int $studentId): array
    {
        $sql = "SELECT status, COUNT(*) as total FROM attendance WHERE student_id = :student_id GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);

        $summary = ['present' => 0, 'absent' => 0, 'on_duty' => 0, 'leave' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $summary[$row['status']] = (int) $row['total'];
        }
        return $summary;
    }
}


