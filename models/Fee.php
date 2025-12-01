<?php
declare(strict_types=1);

namespace Nexera\Models;

class Fee extends BaseModel
{
    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM fees WHERE student_id = :student_id ORDER BY due_date DESC');
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $feeId, string $status, float $amountPaid, ?string $method = null): bool
    {
        $sql = 'UPDATE fees SET status = :status, amount_paid = :amount_paid, payment_method = :method, updated_at = NOW()
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status'      => $status,
            ':amount_paid' => $amountPaid,
            ':method'      => $method,
            ':id'          => $feeId,
        ]);
    }
}


