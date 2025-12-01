<?php
declare(strict_types=1);

namespace Nexera\Models;

class Placement extends BaseModel
{
    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM placements WHERE student_id = :student_id ORDER BY date_applied DESC');
        $stmt->execute([':student_id' => $studentId]);

        return $stmt->fetchAll();
    }

    public function recentOpportunities(int $limit = 5): array
    {
        $stmt = $this->db->prepare('SELECT * FROM placements ORDER BY date_applied DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO placements (student_id, company, position, status, date_applied, notes)
             VALUES (:student_id, :company, :position, :status, :date_applied, :notes)'
        );

        return $stmt->execute([
            ':student_id'   => $data['student_id'],
            ':company'      => $data['company'],
            ':position'     => $data['position'],
            ':status'       => $data['status'] ?? 'applied',
            ':date_applied' => $data['date_applied'],
            ':notes'        => $data['notes'] ?? null,
        ]);
    }
}


