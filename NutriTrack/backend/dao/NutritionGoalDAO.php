
<?php

require_once __DIR__ . '/BaseDAO.php';

class NutritionGoalDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'nutrition_goals',
            'goal_id',
            [
                'user_id',
                'goal_type',
                'target_value'
            ]
        );
    }

    // Get all nutrition goals for a user
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM nutrition_goals
                WHERE user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
