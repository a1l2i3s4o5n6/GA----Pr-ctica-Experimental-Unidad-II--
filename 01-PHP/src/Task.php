<?php

namespace App;

use PDO;

class Task
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, string $title, string $description, ?string $dueDate): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tasks (user_id, title, description, due_date, status)
             VALUES (:user_id, :title, :description, :due_date, :status)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate ?: null,
            'status' => 'pending',
        ]);

        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    public function getAll(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();
        return $task ?: null;
    }

    public function update(int $id, array $data): array
    {
        $stmt = $this->db->prepare(
            'UPDATE tasks SET title = :title, description = :description,
             status = :status, due_date = :due_date, updated_at = NOW()
             WHERE id = :id'
        );
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'due_date' => $data['due_date'] ?: null,
            'id' => $id,
        ]);

        return ['success' => true];
    }

    public function delete(int $id): array
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return ['success' => true];
    }
}
