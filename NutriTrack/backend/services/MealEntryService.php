
<?php

require_once __DIR__ . '/../dao/MealEntryDAO.php';

class MealEntryService
{
    private MealEntryDAO $mealEntryDAO;

    public function __construct(MealEntryDAO $mealEntryDAO)
    {
        $this->mealEntryDAO = $mealEntryDAO;
    }

    // Get all meal entries
    public function getAllEntries(): array
    {
        return $this->mealEntryDAO->getAll();
    }

    // Get entry by ID
    public function getEntryById(int $id): array|false
    {
        return $this->mealEntryDAO->getById($id);
    }

    // Get all food entries belonging to a meal
    public function getEntriesByMealId(int $mealId): array
    {
        if ($mealId <= 0) {
            throw new InvalidArgumentException(
                "Invalid meal ID."
            );
        }

        return $this->mealEntryDAO->getByMealId($mealId);
    }

    // Add food to a meal
    public function addFoodToMeal(
        int $mealId,
        int $foodId,
        float $servingGrams
    ): string {

        if ($mealId <= 0 || $foodId <= 0) {
            throw new InvalidArgumentException(
                "Invalid meal ID or food ID."
            );
        }

        if ($servingGrams <= 0) {
            throw new InvalidArgumentException(
                "Serving grams must be greater than zero."
            );
        }

        return $this->mealEntryDAO->create([
            'meal_id' => $mealId,
            'food_id' => $foodId,
            'serving_grams' => $servingGrams
        ]);
    }

    // Update a meal entry
    public function updateEntry(int $id, array $data): bool
{
    if ($id <= 0) {
        throw new InvalidArgumentException(
            "Invalid entry ID."
        );
    }

    if (!isset($data['serving_grams'])) {
        throw new InvalidArgumentException(
            "Serving grams are required."
        );
    }

    $servingGrams = (float) $data['serving_grams'];

    if (!is_finite($servingGrams) || $servingGrams <= 0 || $servingGrams > 5000) {
        throw new InvalidArgumentException(
            "Serving grams must be between 1 and 5000."
        );
    }

    return $this->mealEntryDAO->update($id, [
        'serving_grams' => $servingGrams
    ]);
}

    // Remove food from a meal
    public function removeFoodFromMeal(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid entry ID."
            );
        }

        return $this->mealEntryDAO->delete($id);
    }
}
