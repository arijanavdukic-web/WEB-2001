
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/FoodService.php';

Flight::route('GET /api/foods', function () {
    try {
        $db = Database::connect();
        $service = new FoodService($db);

        $request = Flight::request();
        $search = $request->query->search;
        $id = $request->query->id;

        if ($search !== null) {
            $foods = $service->searchFoods((string) $search);
            Flight::json($foods, 200);
            return;
        }

        if ($id !== null) {
            $food = $service->getFoodById((int) $id);

            if ($food === false) {
                Flight::json(['error' => 'Food not found.'], 404);
                return;
            }

            Flight::json($food, 200);
            return;
        }

        Flight::json($service->getAllFoods(), 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('POST /api/foods', function () {
    try {
        $db = Database::connect();
        $service = new FoodService($db);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $foodId = $service->createFood($data);

        Flight::json([
            'message' => 'Food created successfully.',
            'food_id' => $foodId
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('PUT /api/foods/@id', function ($id) {
    try {
        $db = Database::connect();
        $service = new FoodService($db);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $updated = $service->updateFood((int) $id, $data);

        if (!$updated) {
            Flight::json(['error' => 'Food not found or not updated.'], 404);
            return;
        }

        Flight::json(['message' => 'Food updated successfully.'], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

Flight::route('DELETE /api/foods/@id', function ($id) {
    try {
        $db = Database::connect();
        $service = new FoodService($db);

        $deleted = $service->deleteFood((int) $id);

        if (!$deleted) {
            Flight::json(['error' => 'Food not found.'], 404);
            return;
        }

        Flight::json(['message' => 'Food deleted successfully.'], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});
