<?php
/**
 * Database Connection Handler - SIMS
 * Handles all database connections and queries
 */

require_once 'config.php';

class Database {
    private $connection;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $port = DB_PORT;

    public function connect() {
        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->db_name,
            $this->port
        );

        // Check connection
        if ($this->connection->connect_error) {
            die("Database Connection Failed: " . $this->connection->connect_error);
        }

        // Set charset to UTF-8
        $this->connection->set_charset("utf8mb4");

        return $this->connection;
    }

    public function getConnection() {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Execute a prepared statement query
     */
    public function prepare($query) {
        return $this->getConnection()->prepare($query);
    }

    /**
     * Execute a query without parameters
     */
    public function execute($query) {
        $result = $this->getConnection()->query($query);
        if ($this->getConnection()->error) {
            throw new Exception($this->getConnection()->error);
        }
        return $result;
    }

    /**
     * Get last inserted ID
     */
    public function getLastInsertId() {
        return $this->getConnection()->insert_id;
    }

    /**
     * Get number of affected rows
     */
    public function getAffectedRows() {
        return $this->getConnection()->affected_rows;
    }

    /**
     * Escape string for safe SQL
     */
    public function escape($string) {
        return $this->getConnection()->real_escape_string($string);
    }
}

// Create global database instance
$db = new Database();
$db->connect();
