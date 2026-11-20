
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../dao/NutritionGoalDAO.php';
require_once __DIR__ . '/../services/NutritionGoalService.php';

// GET nutrition goals
Flight::route('GET /api/nutrition-goals', function () {

    try {
        $db = Database::connect();
        $dao = new NutritionGoalDAO($db);
        $service = new NutritionGoalService($dao);

        $userId = Flight::request()->query->user_id;
        $id = Flight::request()->query->id;

        if ($userId !== null) {
            Flight::json($service->getGoalsByUserId((int) $userId));
            return;
        }

        if ($id !== null) {
            $goal = $service->getGoalById((int) $id);

            if ($goal === false) {
                Flight::json(['error' => 'Nutrition goal not found.'], 404);
                return;
            }

            Flight::json($goal);
            return;
        }

        Flight::json($service->getAllGoals());

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// POST create nutrition goal
Flight::route('POST /api/nutrition-goals', function () {

    try {
        $db = Database::connect();
        $dao = new NutritionGoalDAO($db);
        $service = new NutritionGoalService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $goalId = $service->createGoal(
            (int) ($data['user_id'] ?? 0),
            (string) ($data['goal_type'] ?? ''),
            (float) ($data['target_value'] ?? 0)
        );

        Flight::json([
            'message' => 'Nutrition goal created successfully.',
            'goal_id' => $goalId
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// PUT update nutrition goal
Flight::route('PUT /api/nutrition-goals/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new NutritionGoalDAO($db);
        $service = new NutritionGoalService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $updated = $service->updateGoal((int) $id, $data);

        if (!$updated) {
            Flight::json([
                'error' => 'Nutrition goal not found or not updated.'
            ], 404);
            return;
        }

        Flight::json([
            'message' => 'Nutrition goal updated successfully.'
        ]);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// DELETE nutrition goal
Flight::route('DELETE /api/nutrition-goals/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new NutritionGoalDAO($db);
        $service = new NutritionGoalService($dao);

        $deleted = $service->deleteGoal((int) $id);

        if (!$deleted) {
            Flight::json(['error' => 'Nutrition goal not found.'], 404);
            return;
        }

        Flight::json([
            'message' => 'Nutrition goal deleted successfully.'
        ]);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});
