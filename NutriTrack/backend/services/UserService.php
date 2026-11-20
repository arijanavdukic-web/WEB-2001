<?php

require_once __DIR__ . '/../dao/UserDAO.php';

class UserService
{
    private UserDAO $userDAO;

    public function __construct(UserDAO $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    // Remove private password data before returning a user.
    private function sanitizeUser(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }

    public function getAllUsers(): array
    {
        return array_map(
            fn(array $user) => $this->sanitizeUser($user),
            $this->userDAO->findAll()
        );
    }

    public function getUserById(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('User ID must be greater than zero.');
        }

        $user = $this->userDAO->findById($id);

        return $user ? $this->sanitizeUser($user) : false;
    }

    public function register(string $name, string $email, string $password): string
    {
        $name = trim($name);
        $email = strtolower(trim($email));

        if ($name === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException('All fields are required.');
        }

        if (strlen($name) > 100) {
            throw new InvalidArgumentException('Name cannot exceed 100 characters.');
        }

        if (strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must contain at least 8 characters.');
        }

        if ($this->userDAO->getByEmail($email)) {
            throw new InvalidArgumentException('Email is already registered.');
        }

        return $this->userDAO->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function login(string $email, string $password): array|false
    {
        $email = strtolower(trim($email));

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException('Email and password are required.');
        }

        $user = $this->userDAO->getByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        return $this->sanitizeUser($user);
    }

    public function updateUser(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('User ID must be greater than zero.');
        }

        $existingUser = $this->userDAO->findById($id);

        if (!$existingUser) {
            return false;
        }

        $updateData = [];

        if (array_key_exists('name', $data)) {
            $name = trim((string) $data['name']);

            if ($name === '' || strlen($name) > 100) {
                throw new InvalidArgumentException('Name must contain 1 to 100 characters.');
            }

            $updateData['name'] = $name;
        }

        if (array_key_exists('email', $data)) {
            $email = strtolower(trim((string) $data['email']));

            if (strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Invalid email address.');
            }

            $userWithEmail = $this->userDAO->getByEmail($email);

            if ($userWithEmail && (int) $userWithEmail['user_id'] !== $id) {
                throw new InvalidArgumentException('Email is already registered.');
            }

            $updateData['email'] = $email;
        }

        if (array_key_exists('password', $data) && $data['password'] !== '') {
            $password = (string) $data['password'];

            if (strlen($password) < 8) {
                throw new InvalidArgumentException('Password must contain at least 8 characters.');
            }

            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException('No valid fields were provided for update.');
        }

        return $this->userDAO->updateUser($id, $updateData);
    }

    public function deleteUser(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('User ID must be greater than zero.');
        }

        return $this->userDAO->deleteUser($id);
    }
}