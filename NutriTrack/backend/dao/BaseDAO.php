
<?php

class BaseDAO
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey;
    protected array $allowedColumns;

    public function __construct(
        PDO $db,
        string $table,
        string $primaryKey,
        array $allowedColumns
    ) {
        $this->db = $db;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->allowedColumns = $allowedColumns;
    }

    // Get all records
    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get one record by ID
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Create a new record
    public function create(array $data): string
    {
        $columns = array_keys($data);

        foreach ($columns as $column) {
            if (!in_array($column, $this->allowedColumns, true)) {
                throw new InvalidArgumentException(
                    "Invalid column: $column"
                );
            }
        }

        if (empty($columns)) {
            throw new InvalidArgumentException(
                "No data provided."
            );
        }

        $columnNames = implode(', ', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO {$this->table}
                ($columnNames)
                VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    // Update an existing record
    public function update(int $id, array $data): bool
    {
        $columns = array_keys($data);

        if (empty($columns)) {
            throw new InvalidArgumentException(
                "No data provided."
            );
        }

        foreach ($columns as $column) {
            if (!in_array($column, $this->allowedColumns, true)) {
                throw new InvalidArgumentException(
                    "Invalid column: $column"
                );
            }
        }

        $setParts = [];

        foreach ($columns as $column) {
            $setParts[] = "$column = :$column";
        }

        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table}
                SET $setClause
                WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    // Delete a record
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table}
                WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}
