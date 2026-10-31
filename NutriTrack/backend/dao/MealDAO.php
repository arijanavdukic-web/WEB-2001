
<?php

require_once __DIR__ . '/BaseDAO.php';

class MealDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'meals',
            'meal_id',
            [
                'user_id',
                'meal_date',
                'meal_type'
            ]
        );
    }

    // Get all meals belonging to a user
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM meals
                WHERE user_id = :user_id
                ORDER BY meal_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
