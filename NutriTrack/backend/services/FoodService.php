<?php

require_once __DIR__ . '/../dao/FoodDAO.php';

class FoodService
{
    private FoodDAO $foodDAO;

    public function __construct(PDO $db)
    {
        $this->foodDAO = new FoodDAO($db);
    }

    public function getAllFoods(): array
    {
        return $this->foodDAO->getAll();
    }

    public function getFoodById(int $foodId): array|false
    {
        if ($foodId <= 0) {
            throw new InvalidArgumentException("Invalid food ID.");
        }

        return $this->foodDAO->getById($foodId);
    }

    public function searchFoods(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException("Food name cannot be empty.");
        }

        return $this->foodDAO->searchByName($name);
    }

    public function createFood(array $data): string
    {
        if (empty(trim($data['name'] ?? ''))) {
            throw new InvalidArgumentException("Food name is required.");
        }

        if (($data['calories_per_100g'] ?? 0) < 0) {
            throw new InvalidArgumentException("Calories cannot be negative.");
        }

        if (($data['protein_per_100g'] ?? 0) < 0) {
            throw new InvalidArgumentException("Protein cannot be negative.");
        }

        if (($data['carbohydrates_per_100g'] ?? 0) < 0) {
            throw new InvalidArgumentException("Carbohydrates cannot be negative.");
        }

        if (($data['fat_per_100g'] ?? 0) < 0) {
            throw new InvalidArgumentException("Fat cannot be negative.");
        }

        return $this->foodDAO->create($data);
    }

    public function updateFood(int $foodId, array $data): bool
    {
        if ($foodId <= 0) {
            throw new InvalidArgumentException("Invalid food ID.");
        }

        if (empty($data)) {
            throw new InvalidArgumentException("No data provided.");
        }

        if (isset($data['name']) && trim($data['name']) === '') {
            throw new InvalidArgumentException("Food name cannot be empty.");
        }

        foreach ([
            'calories_per_100g',
            'protein_per_100g',
            'carbohydrates_per_100g',
            'fat_per_100g'
        ] as $field) {
            if (isset($data[$field]) && $data[$field] < 0) {
                throw new InvalidArgumentException("$field cannot be negative.");
            }
        }

        return $this->foodDAO->update($foodId, $data);
    }

    public function deleteFood(int $foodId): bool
    {
        if ($foodId <= 0) {
            throw new InvalidArgumentException("Invalid food ID.");
        }

        return $this->foodDAO->delete($foodId);
    }
}