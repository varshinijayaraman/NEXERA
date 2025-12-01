<?php
declare(strict_types=1);

namespace Nexera\Models;

class Task extends BaseModel
{
    public function getAssignedToUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tasks WHERE assigned_to_user_id = :user_id ORDER BY due_date ASC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO tasks (assigned_by, assigned_to_user_id, title, description, due_date, status)
                VALUES (:assigned_by, :assigned_to, :title, :description, :due_date, :status)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':assigned_by' => $data['assigned_by'],
            ':assigned_to' => $data['assigned_to_user_id'],
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':due_date'    => $data['due_date'],
            ':status'      => $data['status'] ?? 'assigned',
        ]);
    }

    public function updateStatus(int $taskId, string $status, ?string $feedback = null, ?string $grade = null): bool
    {
        $sql = 'UPDATE tasks SET status = :status, feedback = :feedback, grade = :grade WHERE id = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status'   => $status,
            ':feedback' => $feedback,
            ':grade'    => $grade,
            ':id'       => $taskId,
        ]);
    }
}


