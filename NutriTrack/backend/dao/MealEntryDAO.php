
<?php

require_once __DIR__ . '/BaseDAO.php';

class MealEntryDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'meal_entries',
            'entry_id',
            [
                'meal_id',
                'food_id',
                'serving_grams'
            ]
        );
    }

    // Get all food entries belonging to a meal
    public function getByMealId(int $mealId): array
    {
        $sql = "SELECT
                    meal_entries.*,
                    foods.name AS food_name,
                    foods.calories_per_100g
                FROM meal_entries
                INNER JOIN foods
                    ON meal_entries.food_id = foods.food_id
                WHERE meal_entries.meal_id = :meal_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['meal_id' => $mealId]);

        return $stmt->fetchAll();
    }
}
