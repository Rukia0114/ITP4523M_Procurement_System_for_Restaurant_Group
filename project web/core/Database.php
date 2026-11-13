<?php
/**
 * Database Connection Class
 * Encapsulates database operations for better security and maintainability
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct() {
        $this->config = require __DIR__ . '/../config/database.php';
        try {
            $this->connection = new mysqli(
                $this->config['host'],
                $this->config['username'],
                $this->config['password'],
                $this->config['database'],
                $this->config['port'],
                $this->config['options']
            );

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }

            $this->connection->set_charset($this->config['charset']);
        } catch (Exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            if (!$this->config['debug']) {
                die("Database connection failed. Please contact administrator.");
            }
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }

    /**
     * Prepared statement query with parameter binding
     */
    public function query(string $sql, array $params): ?array {
        try {
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                return null;
            }

            $types = '';
            foreach ($params as $param) {
                if (is_string($param)) {
                    $types .= 's';
                } elseif (is_int($param)) {
                    $types .= 'i';
                } elseif (is_bool($param)) {
                    $types .= 'b';
                } else {
                    $types .= 's'; // default to string
                }
            }

            if ($types !== '') {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result === true) {
                    // INSERT/UPDATE/DELETE - return affected rows
                    return ['success' => true, 'affected_rows' => $stmt->affected_rows];
                }
                
                // SELECT - return results
                return ['success' => true, 'data' => $result->fetch_all(MYSQLI_ASSOC)];
            }
            
            return ['success' => false, 'error' => $stmt->error];
        } catch (Exception $e) {
            error_log("Database Query Error: " . $e->getMessage());
            if ($this->config['debug']) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
            return ['success' => false, 'error' => 'Database query failed'];
        }
    }

    /**
     * Execute INSERT/UPDATE/DELETE statement
     */
    public function execute(string $sql): array {
        return $this->query($sql);
    }

    public function getConnectionError(): ?string {
        return $this->connection->connect_error;
    }

    public function lastInsertId(): ?int {
        return $this->connection->insert_id;
    }
}
