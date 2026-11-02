
<?php

require_once __DIR__ . '/BaseDAO.php';

class DailySummaryDAO extends BaseDAO
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'daily_summaries',
            'summary_id',
            [
                'user_id',
                'summary_date',
                'total_calories'
            ]
        );
    }

    // Get summaries belonging to a user
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM daily_summaries
                WHERE user_id = :user_id
                ORDER BY summary_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    // Get a user's summary for a specific date
    public function getByUserAndDate(
        int $userId,
        string $date
    ): array|false {
        $sql = "SELECT * FROM daily_summaries
                WHERE user_id = :user_id
                AND summary_date = :summary_date";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'summary_date' => $date
        ]);

        return $stmt->fetch();
    }
}
