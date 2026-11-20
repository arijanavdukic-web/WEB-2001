
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../dao/DailySummaryDAO.php';
require_once __DIR__ . '/../services/DailySummaryService.php';

// GET daily summaries
Flight::route('GET /api/daily-summaries', function () {

    try {
        $db = Database::connect();
        $dao = new DailySummaryDAO($db);
        $service = new DailySummaryService($dao);

        $userId = Flight::request()->query->user_id;
        $date = Flight::request()->query->date;
        $id = Flight::request()->query->id;

        if ($userId !== null && $date !== null) {
            $summary = $service->getSummaryByUserAndDate(
                (int) $userId,
                (string) $date
            );

            if ($summary === false) {
                Flight::json(['error' => 'Daily summary not found.'], 404);
                return;
            }

            Flight::json($summary);
            return;
        }

        if ($userId !== null) {
            Flight::json($service->getSummariesByUserId((int) $userId));
            return;
        }

        if ($id !== null) {
            $summary = $service->getSummaryById((int) $id);

            if ($summary === false) {
                Flight::json(['error' => 'Daily summary not found.'], 404);
                return;
            }

            Flight::json($summary);
            return;
        }

        Flight::json($service->getAllSummaries());

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// POST create daily summary
Flight::route('POST /api/daily-summaries', function () {

    try {
        $db = Database::connect();
        $dao = new DailySummaryDAO($db);
        $service = new DailySummaryService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $summaryId = $service->createSummary(
            (int) ($data['user_id'] ?? 0),
            (string) ($data['summary_date'] ?? ''),
            (float) ($data['total_calories'] ?? 0)
        );

        Flight::json([
            'message' => 'Daily summary created successfully.',
            'summary_id' => $summaryId
        ], 201);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// PUT update daily summary
Flight::route('PUT /api/daily-summaries/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new DailySummaryDAO($db);
        $service = new DailySummaryService($dao);

        $data = Flight::request()->data->getData();

        if (!is_array($data) || empty($data)) {
            Flight::json(['error' => 'Invalid JSON data.'], 400);
            return;
        }

        $updated = $service->updateSummary((int) $id, $data);

        if (!$updated) {
            Flight::json([
                'error' => 'Daily summary not found or not updated.'
            ], 404);
            return;
        }

        Flight::json([
            'message' => 'Daily summary updated successfully.'
        ]);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});

// DELETE daily summary
Flight::route('DELETE /api/daily-summaries/@id', function ($id) {

    try {
        $db = Database::connect();
        $dao = new DailySummaryDAO($db);
        $service = new DailySummaryService($dao);

        $deleted = $service->deleteSummary((int) $id);

        if (!$deleted) {
            Flight::json(['error' => 'Daily summary not found.'], 404);
            return;
        }

        Flight::json([
            'message' => 'Daily summary deleted successfully.'
        ]);

    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        Flight::json(['error' => 'Internal server error.'], 500);
    }
});
