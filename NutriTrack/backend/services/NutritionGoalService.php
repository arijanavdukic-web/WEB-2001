
<?php

require_once __DIR__ . '/../dao/NutritionGoalDAO.php';

class NutritionGoalService
{
    private NutritionGoalDAO $nutritionGoalDAO;

    public function __construct(NutritionGoalDAO $nutritionGoalDAO)
    {
        $this->nutritionGoalDAO = $nutritionGoalDAO;
    }

    // Get all nutrition goals
    public function getAllGoals(): array
    {
        return $this->nutritionGoalDAO->getAll();
    }

    // Get goal by ID
    public function getGoalById(int $id): array|false
    {
        return $this->nutritionGoalDAO->getById($id);
    }

    // Get goals belonging to a user
    public function getGoalsByUserId(int $userId): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        return $this->nutritionGoalDAO->getByUserId($userId);
    }

    // Create a nutrition goal
    public function createGoal(
        int $userId,
        string $goalType,
        float $targetValue
    ): string {

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

    $allowedGoalTypes = [
    'daily_calories',
    'daily_protein'
    ];

        if (!in_array($goalType, $allowedGoalTypes, true)) {
            throw new InvalidArgumentException(
                "Invalid goal type."
            );
        }

        if ($targetValue <= 0) {
            throw new InvalidArgumentException(
                "Target value must be greater than zero."
            );
        }

        return $this->nutritionGoalDAO->create([
            'user_id' => $userId,
            'goal_type' => $goalType,
            'target_value' => $targetValue
        ]);
    }

    // Update a nutrition goal
    public function updateGoal(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid goal ID."
            );
        }

        if (isset($data['target_value']) && $data['target_value'] <= 0) {
            throw new InvalidArgumentException(
                "Target value must be greater than zero."
            );
        }

        if (isset($data['goal_type'])) {
            $allowedGoalTypes = [
            'daily_calories',
            'daily_protein'
           ];

            if (!in_array($data['goal_type'], $allowedGoalTypes, true)) {
                throw new InvalidArgumentException(
                    "Invalid goal type."
                );
            }
        }

        return $this->nutritionGoalDAO->update($id, $data);
    }

    // Delete a nutrition goal
    public function deleteGoal(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid goal ID."
            );
        }

        return $this->nutritionGoalDAO->delete($id);
    }
}
