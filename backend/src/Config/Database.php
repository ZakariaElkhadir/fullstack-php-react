<?php

namespace App\Config;

use PDO;
use PDOException;
use Exception;

class Database
{
    private ?PDO $connection = null;
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

    private function loadEnvironmentVariables(): void
    {
        // Try to find .env file by traversing up the directory tree
        $envFile = $this->findEnvFile();

        if ($envFile && file_exists($envFile)) {
            $this->parseEnvFile($envFile);
        }

        if (isset($_ENV["DATABASE_URL"])) {
            $this->parseDatabaseUrl($_ENV["DATABASE_URL"]);
        } else {
            $this->host =
                $_ENV["DB_HOST"] ?? (getenv("DB_HOST") ?? "localhost");
            $this->username = $_ENV["DB_USER"] ?? (getenv("DB_USER") ?? "");
            $this->password = $_ENV["DB_PASS"] ?? (getenv("DB_PASS") ?? "");
            $this->database = $_ENV["DB_NAME"] ?? (getenv("DB_NAME") ?? "");
            $this->port =
                (int) ($_ENV["DB_PORT"] ?? (getenv("DB_PORT") ?? 3306));
            $this->charset =
                $_ENV["DB_CHARSET"] ?? (getenv("DB_CHARSET") ?? "utf8mb4");
        }
    }

    private function findEnvFile(): ?string
    {
        $currentDir = __DIR__;
        $maxLevels = 5; 
        $level = 0;

        while ($level < $maxLevels) {
            $envPath = $currentDir . DIRECTORY_SEPARATOR . ".env";

            if (file_exists($envPath)) {
                return $envPath;
            }

            $parentDir = dirname($currentDir);

            if ($parentDir === $currentDir) {
                break;
            }

            $currentDir = $parentDir;
            $level++;
        }

        return null;
    }

    private function parseEnvFile(string $envFile): void
    {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new Exception("Unable to read .env file");
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, "#") === 0) {
                continue;
            }

            $parts = explode("=", $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$name, $value] = $parts;
            $name = trim($name);
            $value = trim($value);

            if (preg_match('/^["\'].*["\']$/', $value)) {
                $value = substr($value, 1, -1);
            }

            $_ENV[$name] = $value;
        }
    }

    private function parseDatabaseUrl(string $databaseUrl): void
    {
        $parsedUrl = parse_url($databaseUrl);

        if ($parsedUrl === false) {
            throw new Exception("Invalid DATABASE_URL format");
        }

        $this->host = $parsedUrl["host"] ?? "localhost";
        $this->username = $parsedUrl["user"] ?? "";
        $this->password = $parsedUrl["pass"] ?? "";
        $this->database = ltrim($parsedUrl["path"] ?? "", "/");
        $this->port = (int) ($parsedUrl["port"] ?? 3306);
        $this->charset = "utf8mb4";
    }

    private function connect(): void
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};port={$this->port};charset={$this->charset}";
        try {
            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            );
        } catch (PDOException $e) {
            $this->connection = null;
            throw new Exception(
                "Database connection error: " . $e->getMessage(),
            );
        }
    }

    /**
     * Get the PDO connection instance
     *
     * @return PDO The database connection
     * @throws Exception When no connection is available
     */
    public function getConnection(): PDO
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
        return $this->connection !== null;
    }

    /**
     * Close database connection
     */
    public function close(): void
    {
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->close();
    }
}
