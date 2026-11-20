<?php

require_once __DIR__ . '/BaseDAO.php';

class UserDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'users',
            'user_id',
            [
                'name',
                'email',
                'password_hash'
            ]
        );
    }

    public function getByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }

    public function findAll(): array
    {
        $sql = "SELECT user_id, name, email, created_at FROM users ORDER BY user_id";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $sql = "SELECT user_id, name, email, created_at
                FROM users
                WHERE user_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function updateUser(int $id, array $data): bool
    {
        $allowedColumns = ['name', 'email', 'password_hash'];
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $column => $value) {
            if (!in_array($column, $allowedColumns, true)) {
                throw new InvalidArgumentException('Invalid user field.');
            }

            $fields[] = "$column = :$column";
            $params[$column] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}