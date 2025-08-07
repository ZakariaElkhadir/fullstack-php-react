<?php

namespace App\Config;

use mysqli;
use Exception;

class Database
{
    private ?mysqli $connection = null;
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private int $port;
    private string $charset;

    public function __construct()
    {
        $this->loadEnvironmentVariables();
        $this->connect();
    }

    /**
     * Load environment variables from .env file
     */
    private function loadEnvironmentVariables(): void
    {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                throw new Exception("Unable to read .env file");
            }

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }

                $parts = explode('=', $line, 2);
                if (count($parts) !== 2) {
                    continue;
                }

                [$name, $value] = $parts;
                $name = trim($name);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^["\'].*["\']$/', $value)) {
                    $value = substr($value, 1, -1);
                }

                $_ENV[$name] = $value;
            }
        }

        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->username = $_ENV['DB_USER'] ?? '';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->database = $_ENV['DB_NAME'] ?? '';
        $this->port = (int)($_ENV['DB_PORT'] ?? 3306);
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }

    /**
     * Establish database connection
     *
     * @throws Exception When connection fails
     */
    private function connect(): void
    {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }

            if (!$this->connection->set_charset($this->charset)) {
                throw new Exception("Error setting charset: " . $this->connection->error);
            }

            echo "Database connected successfully!\n";
        } catch (Exception $e) {
            $this->connection = null;
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }

    /**
     * Get the MySQLi connection instance
     *
     * @return mysqli The database connection
     * @throws Exception When no connection is available
     */
    public function getConnection(): mysqli
    {
        if ($this->connection === null) {
            throw new Exception("No database connection available");
        }
        return $this->connection;
    }

    /**
     * Check if database is connected
     */
    public function isConnected(): bool
    {
        return $this->connection !== null && $this->connection->ping();
    }

    /**
     * Close database connection
     */
    public function close(): void
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Destructor - automatically close connection
     */
    public function __destruct()
    {
        $this->close();
    }
}

try {
    $db = new Database();
    if ($db->isConnected()) {
        echo "Database is connected and ready to use.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}