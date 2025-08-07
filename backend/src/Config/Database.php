<?php

namespace App\Config;

use mysqli;
use Exception;

class Database
{
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $charset;

    public function __construct()
    {
        $this->loadEnvironementVariables();
        $this->connect();
    }

    /**
     * Load environment variables from .env file
     */
    private function loadEnvironementVariables()
    {
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

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
     */
    private function connect()
    {
        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            if (!$this->connection->set_charset($this->charset)) {
                throw new Exception("Error setting charset: " . $this->connection->error);
            }

            echo "Database connected successfully!\n";
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    /**
     * Get the MySQLi connection instance
     */
    public function getConnection() {
        return $this->connection;
    }
    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Destructor - automatically close connection
     */
    public function __destruct() {
        $this->close();
    }
}

}