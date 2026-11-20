<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../routes/external_food.php';
require_once __DIR__ . '/../routes/food.php';
require_once __DIR__ . '/../routes/meal.php';
require_once __DIR__ . '/../routes/meal_entry.php';
require_once __DIR__ . '/../routes/daily_summary.php';
require_once __DIR__ . '/../routes/nutrition_goal.php';
require_once __DIR__ . '/../routes/user.php';

// Basic API test route
Flight::route('GET /api', function () {
    Flight::json([
        'success' => true,
        'message' => 'NutriTrack API is running with FlightPHP.'
    ]);
});

// Start FlightPHP
Flight::start();