<?php

class DatabaseFunctions {
    private $db;
    private $security;

    public function __construct($db, $security) {
        $this->db = $db;
        $this->security = $security;
    }

    /**
     * Get a single record from the database
     */
    public function getRecord($table, $column, $value) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $column = :value LIMIT 1");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get multiple records from the database
     */
    public function getRecords($table, $column = null, $value = null, $orderBy = null, $limit = null) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT * FROM $table";
        
        if ($column !== null && $value !== null) {
            $sql .= " WHERE $column = :value";
        }
        
        if ($orderBy !== null) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }
        
        $stmt = $pdo->prepare($sql);
        
        if ($column !== null && $value !== null) {
            $stmt->bindParam(':value', $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a record into the database
     */
    public function insertRecord($table, $data) {
        $pdo = $this->db->getConnection();
        
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Update a record in the database
     */
    public function updateRecord($table, $data, $whereColumn, $whereValue) {
        $pdo = $this->db->getConnection();
        
        $setClause = "";
        foreach ($data as $key => $value) {
            $setClause .= "$key = :$key, ";
        }
        $setClause = rtrim($setClause, ", ");
        
        $sql = "UPDATE $table SET $setClause WHERE $whereColumn = :whereValue";
        $stmt = $pdo->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":whereValue", $whereValue);
        
        return $stmt->execute();
    }

    /**
     * Delete a record from the database
     */
    public function deleteRecord($table, $column, $value) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("DELETE FROM $table WHERE $column = :value");
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }
}
?> 