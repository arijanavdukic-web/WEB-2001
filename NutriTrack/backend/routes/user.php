<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../services/UserService.php';

Flight::route('GET /api/users', function () {
    try {
        $db = Database::connect();
        $service = new UserService(new UserDAO($db));

        Flight::json([
            'success' => true,
            'users' => $service->getAllUsers()
        ], 200);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('GET /api/users/@id', function ($id) {
    try {
        $service = new UserService(new UserDAO(Database::connect()));
        $user = $service->getUserById((int) $id);

        if (!$user) {
            Flight::json(['error' => 'User not found.'], 404);
            return;
        }

        Flight::json(['success' => true, 'user' => $user], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('POST /api/users', function () {
    try {
        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $service = new UserService(new UserDAO(Database::connect()));
        $action = $data['action'] ?? '';

        if ($action === 'register') {
            $userId = $service->register(
                (string) ($data['name'] ?? ''),
                (string) ($data['email'] ?? ''),
                (string) ($data['password'] ?? '')
            );

            Flight::json([
                'message' => 'User registered successfully.',
                'user_id' => $userId
            ], 201);

            return;
        }

        if ($action === 'login') {
            $user = $service->login(
                (string) ($data['email'] ?? ''),
                (string) ($data['password'] ?? '')
            );

            if ($user === false) {
                Flight::json(['error' => 'Invalid email or password.'], 401);
                return;
            }

            Flight::json([
                'message' => 'Login successful.',
                'user' => $user
            ], 200);

            return;
        }

        Flight::json([
            'error' => 'Invalid action. Use register or login.'
        ], 400);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('PUT /api/users/@id', function ($id) {
    try {
        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $service = new UserService(new UserDAO(Database::connect()));

        if (!$service->updateUser((int) $id, $data)) {
            Flight::json(['error' => 'User not found.'], 404);
            return;
        }

        Flight::json(['message' => 'User updated successfully.'], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('DELETE /api/users/@id', function ($id) {
    try {
        $service = new UserService(new UserDAO(Database::connect()));

        if (!$service->deleteUser((int) $id)) {
            Flight::json(['error' => 'User not found.'], 404);
            return;
        }

        Flight::json(['message' => 'User deleted successfully.'], 200);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});