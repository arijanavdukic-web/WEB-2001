<?php

require_once __DIR__ . '/../services/FoodExternalService.php';

Flight::route('GET /api/external/foods', function () {
    try {
       $query = trim(Flight::request()->query->query ?? '');

    $pageSize = (int) (Flight::request()->query->pageSize ?? 10);

    if ($query === '') {
        throw new InvalidArgumentException('Search query is required.');
    }

    if ($pageSize < 1 || $pageSize > 25) {
        throw new InvalidArgumentException(
            'Page size must be between 1 and 25.'
        );
    }

        $service = new FoodExternalService();
        $foods = $service->searchFoods($query, $pageSize);

        Flight::json([
            'success' => true,
            'count' => count($foods),
            'data' => $foods
        ], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        Flight::json([
            'success' => false,
            'message' => 'External food service is temporarily unavailable.'
        ], 502);
    }
});