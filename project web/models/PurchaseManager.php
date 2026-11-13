<?php
/**
 * Purchase Manager Model
 */

class PurchaseManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get manager by ID (for login)
     */
    public function getByID(string $id): ?array {
        $sql = "SELECT purchaseManagerID, managerName, contactNumber, warehouseAddress 
                FROM purchasemanager WHERE purchaseManagerID = ?";
        
        $result = $this->db->query($sql, [$id]);
        
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        
        return null;
    }

    /**
     * Get all managers (for admin purposes)
     */
    public function getAll(): array {
        $sql = "SELECT purchaseManagerID, managerName, contactNumber, warehouseAddress 
                FROM purchasemanager";
        
        return $this->db->query($sql)['data'] ?? [];
    }

    /**
     * Update manager information
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

        $sql = "UPDATE purchasemanager SET " . implode(', ', $fields) . " WHERE purchaseManagerID = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /**
     * Validate field name
     */
    private function isValidField(string $field): bool {
        $allowedFields = ['managerName', 'contactNumber', 'warehouseAddress'];
        return in_array($field, $allowedFields);
    }
}
