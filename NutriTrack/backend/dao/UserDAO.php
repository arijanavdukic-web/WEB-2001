
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

    // Find a user by email
    public function getByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM users
                WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }
}
