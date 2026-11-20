
<?php

require_once __DIR__ . '/../dao/DailySummaryDAO.php';

class DailySummaryService
{
    private DailySummaryDAO $dailySummaryDAO;

    public function __construct(DailySummaryDAO $dailySummaryDAO)
    {
        $this->dailySummaryDAO = $dailySummaryDAO;
    }

    // Get all daily summaries
    public function getAllSummaries(): array
    {
        return $this->dailySummaryDAO->getAll();
    }

    // Get summary by ID
    public function getSummaryById(int $id): array|false
    {
        return $this->dailySummaryDAO->getById($id);
    }

    // Get summaries belonging to a user
    public function getSummariesByUserId(int $userId): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        return $this->dailySummaryDAO->getByUserId($userId);
    }

    // Get summary for a specific user and date
    public function getSummaryByUserAndDate(
        int $userId,
        string $date
    ): array|false {

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        $dateObject = DateTime::createFromFormat('!Y-m-d', $date);

        if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException(
                "Invalid date format. Use YYYY-MM-DD."
            );
        }

        return $this->dailySummaryDAO->getByUserAndDate(
            $userId,
            $date
        );
    }

    // Create a daily summary
    public function createSummary(
        int $userId,
        string $date,
        float $totalCalories
    ): string {

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid user ID."
            );
        }

        $dateObject = DateTime::createFromFormat('!Y-m-d', $date);

        if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException(
                "Invalid date format. Use YYYY-MM-DD."
            );
        }

        if ($totalCalories < 0) {
            throw new InvalidArgumentException(
                "Calories cannot be negative."
            );
        }

        if ($this->dailySummaryDAO->getByUserAndDate($userId, $date)) {
            throw new InvalidArgumentException(
                "Daily summary already exists for this date."
            );
        }

        return $this->dailySummaryDAO->create([
            'user_id' => $userId,
            'summary_date' => $date,
            'total_calories' => $totalCalories
        ]);
    }

    // Update a daily summary
    public function updateSummary(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid summary ID."
            );
        }

        if (isset($data['total_calories']) && $data['total_calories'] < 0) {
            throw new InvalidArgumentException(
                "Calories cannot be negative."
            );
        }

        return $this->dailySummaryDAO->update($id, $data);
    }

    // Delete a daily summary
    public function deleteSummary(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid summary ID."
            );
        }

        return $this->dailySummaryDAO->delete($id);
    }
}
