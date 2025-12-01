<?php
declare(strict_types=1);

namespace Nexera\Models;

class Internal extends BaseModel
{
    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM internals WHERE student_id = :student_id ORDER BY term DESC, subject ASC');
        $stmt->execute([':student_id' => $studentId]);

        return $stmt->fetchAll();
    }

    public function upsert(array $data): bool
    {
        $sql = 'INSERT INTO internals (student_id, subject, marks, max_marks, term, feedback)
                VALUES (:student_id, :subject, :marks, :max_marks, :term, :feedback)
                ON DUPLICATE KEY UPDATE marks = VALUES(marks), max_marks = VALUES(max_marks), feedback = VALUES(feedback)';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':student_id' => $data['student_id'],
            ':subject'    => $data['subject'],
            ':marks'      => $data['marks'],
            ':max_marks'  => $data['max_marks'],
            ':term'       => $data['term'],
            ':feedback'   => $data['feedback'] ?? null,
        ]);
    }
}


