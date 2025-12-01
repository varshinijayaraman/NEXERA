<?php
declare(strict_types=1);

namespace Nexera\Models;

use PDOException;

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $username, string $passwordHash, string $role, string $status = 'active'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, password_hash, role, status, created_at) VALUES (:username, :password_hash, :role, :status, NOW())'
        );

        $stmt->execute([
            ':username'      => $username,
            ':password_hash' => $passwordHash,
            ':role'          => $role,
            ':status'        => $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash, status = :status WHERE id = :id');
        return $stmt->execute([
            ':password_hash' => $passwordHash,
            ':status'        => 'active',
            ':id'            => $userId,
        ]);
    }

    public function recordLogin(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
        $stmt->execute([':id' => $userId]);
    }
}


