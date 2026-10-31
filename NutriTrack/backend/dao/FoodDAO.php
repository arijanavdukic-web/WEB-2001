
<?php

require_once __DIR__ . '/BaseDAO.php';

class FoodDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'foods',
            'food_id',
            [
                'name',
                'calories_per_100g',
                'protein_per_100g',
                'carbohydrates_per_100g',
                'fat_per_100g'
            ]
        );
    }

    // Search foods by name
    public function searchByName(string $name): array
    {
        $sql = "SELECT * FROM foods
                WHERE name LIKE :name
                ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => '%' . $name . '%']);

        return $stmt->fetchAll();
    }
}
