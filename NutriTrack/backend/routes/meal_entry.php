
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../dao/MealEntryDAO.php';
require_once __DIR__ . '/../services/MealEntryService.php';

// GET all meal entries
Flight::route('GET /api/meal-entries', function () {

    try {
        $db = Database::connect();
        $dao = new MealEntryDAO($db);
        $service = new MealEntryService($dao);

        $mealId = Flight::request()->query->meal_id;
        $id = Flight::request()->query->id;

        if ($mealId !== null) {
            Flight::json($service->getEntriesByMealId((int) $mealId));
            return;
        }

        if ($id !== null) {
            $entry = $service->getEntryById((int) $id);

            if ($entry === false) {
                Flight::json(['error' => 'Meal entry not found.'], 404);
                return;
            }

            Flight::json($entry);
            return;
        }

        Flight::json($service->getAllEntries());

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// POST create meal entry
Flight::route('POST /api/meal-entries', function () {

    try {
        $db = Database::connect();
        $dao = new MealEntryDAO($db);
        $service = new MealEntryService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $entryId = $service->addFoodToMeal(
            (int) ($data['meal_id'] ?? 0),
            (int) ($data['food_id'] ?? 0),
            (float) ($data['serving_grams'] ?? 0)
        );

        Flight::json([
            'message' => 'Food added to meal successfully.',
            'entry_id' => $entryId
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// PUT update meal entry
Flight::route('PUT /api/meal-entries/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new MealEntryDAO($db);
        $service = new MealEntryService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $updated = $service->updateEntry((int) $id, $data);

        if (!$updated) {
            Flight::json(['error' => 'Meal entry not found or not updated.'], 404);
            return;
        }

        Flight::json(['message' => 'Meal entry updated successfully.']);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// DELETE meal entry
Flight::route('DELETE /api/meal-entries/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new MealEntryDAO($db);
        $service = new MealEntryService($dao);

        $deleted = $service->removeFoodFromMeal((int) $id);

        if (!$deleted) {
            Flight::json(['error' => 'Meal entry not found.'], 404);
            return;
        }

        Flight::json([
            'message' => 'Food removed from meal successfully.'
        ]);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});
