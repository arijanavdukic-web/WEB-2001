
<?php

require_once __DIR__ . '/../dao/MealDAO.php';

class MealService
{
    private MealDAO $mealDAO;

    public function __construct(MealDAO $mealDAO)
    {
        $this->mealDAO = $mealDAO;
    }

    // Get all meals
    public function getAllMeals(): array
    {
        return $this->mealDAO->getAll();
    }

    // Get meal by ID
    public function getMealById(int $id): array|false
    {
        return $this->mealDAO->getById($id);
    }

    // Get meals belonging to a user
    public function getMealsByUserId(int $userId): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        return $this->mealDAO->getByUserId($userId);
    }

    // Create a new meal
    public function createMeal(
        int $userId,
        string $mealDate,
        string $mealType
    ): string {

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        if (empty($mealDate) || empty($mealType)) {
            throw new InvalidArgumentException(
                "Meal date and meal type are required."
            );
        }

        return $this->mealDAO->create([
            'user_id' => $userId,
            'meal_date' => $mealDate,
            'meal_type' => $mealType
        ]);
    }

    // Update an existing meal
    public function updateMeal(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid meal ID."
            );
        }

        return $this->mealDAO->update($id, $data);
    }

    // Delete a meal
    public function deleteMeal(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid meal ID."
            );
        }

        return $this->mealDAO->delete($id);
    }
}