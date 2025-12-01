<?php
declare(strict_types=1);

namespace Nexera\Models;

class Document extends BaseModel
{
    public function listByVisibility(string $role): array
    {
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE visibility_role IN (\'all\', :role) ORDER BY created_at DESC');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO documents (uploader_user_id, title, filename, file_path, visibility_role)
                VALUES (:uploader_user_id, :title, :filename, :file_path, :visibility_role)';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uploader_user_id' => $data['uploader_user_id'],
            ':title'            => $data['title'],
            ':filename'         => $data['filename'],
            ':file_path'        => $data['file_path'],
            ':visibility_role'  => $data['visibility_role'],
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $document = $stmt->fetch();
        return $document ?: null;
    }
}


