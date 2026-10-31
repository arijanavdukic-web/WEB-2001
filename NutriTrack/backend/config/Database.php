
<?php

class Database
{
    public static function connect(): PDO
    {
        $config = require __DIR__ . '/config.php';

        $host = $config['database']['host'];
        $name = $config['database']['name'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];
        $charset = $config['database']['charset'];

        $dsn = "mysql:host=$host;dbname=$name;charset=$charset";

        try {
            $pdo = new PDO($dsn, $username, $password);

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            $pdo->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );

            return $pdo;

        } catch (PDOException $e) {
            throw new RuntimeException(
                "Database connection failed.",
                0,
                $e
            );
        }
    }
}
