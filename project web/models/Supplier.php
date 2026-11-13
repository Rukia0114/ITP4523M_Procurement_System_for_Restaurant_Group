<?php
/**
 * Supplier Model
 */

class Supplier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get supplier by ID (for login)
     */
    public function getByID(string $id): ?array {
        $sql = "SELECT supplierID, companyName, contactName, contactNumber, address 
                FROM Supplier WHERE supplierID = ?";
        
        $result = $this->db->query($sql, [$id]);
        
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        
        return null;
    }

    /**
     * Get all suppliers (for admin purposes)
     */
    public function getAll(): array {
        $sql = "SELECT supplierID, companyName FROM Supplier";
        
        return $this->db->query($sql)['data'] ?? [];
    }

    /**
     * Update supplier information
     */
    public function update(string $id, array $data): array {
        $fields = [];
        foreach (array_keys($data) as $field) {
            if ($this->isValidField($field)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return ['success' => false, 'error' => 'No valid fields to update'];
        }

        $sql = "UPDATE Supplier SET " . implode(', ', $fields) . " WHERE supplierID = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /**
     * Validate field name
     */
    private function isValidField(string $field): bool {
        $allowedFields = ['companyName', 'contactName', 'contactNumber', 'address'];
        return in_array($field, $allowedFields);
    }
}
