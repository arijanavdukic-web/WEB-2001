
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../dao/MealDAO.php';
require_once __DIR__ . '/../services/MealService.php';

// GET all meals, get by ID, or get by user ID
Flight::route('GET /api/meals', function () {
    try {
        $db = Database::connect();
        $dao = new MealDAO($db);
        $service = new MealService($dao);

        $request = Flight::request();

        $userId = $request->query->user_id;
        $id = $request->query->id;

        if ($userId !== null) {
            Flight::json(
                $service->getMealsByUserId((int) $userId),
                200
            );
            return;
        }

        if ($id !== null) {
            $meal = $service->getMealById((int) $id);

            if ($meal === false) {
                Flight::json(['error' => 'Meal not found.'], 404);
                return;
            }

            Flight::json($meal, 200);
            return;
        }

        Flight::json($service->getAllMeals(), 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// CREATE meal
Flight::route('POST /api/meals', function () {
    try {
        $db = Database::connect();
        $dao = new MealDAO($db);
        $service = new MealService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $mealId = $service->createMeal(
            (int) ($data['user_id'] ?? 0),
            (string) ($data['meal_date'] ?? ''),
            (string) ($data['meal_type'] ?? '')
        );

        Flight::json([
            'message' => 'Meal created successfully.',
            'meal_id' => $mealId
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// UPDATE meal
Flight::route('PUT /api/meals/@id', function ($id) {
    try {
        $db = Database::connect();
        $dao = new MealDAO($db);
        $service = new MealService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $updated = $service->updateMeal((int) $id, $data);

        if (!$updated) {
            Flight::json(
                ['error' => 'Meal not found or not updated.'],
                404
            );
            return;
        }

        Flight::json([
            'message' => 'Meal updated successfully.'
        ], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// DELETE meal
Flight::route('DELETE /api/meals/@id', function ($id) {
    try {
        $db = Database::connect();
        $dao = new MealDAO($db);
        $service = new MealService($dao);

        $deleted = $service->deleteMeal((int) $id);

        if (!$deleted) {
            Flight::json(['error' => 'Meal not found.'], 404);
            return;
        }

        Flight::json([
            'message' => 'Meal deleted successfully.'
        ], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});
