<?php
/**
 * Item Model
 */

class Item {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all items (for order creation)
     */
    public function getAll(): array {
        $sql = "SELECT itemID, supplierID, itemName, ImageFile, itemDescription, 
                       stockItemQty, price 
                FROM Item WHERE stockItemQty > 0 ORDER BY itemName";
        
        return $this->db->query($sql)['data'] ?? [];
    }

    /**
     * Get item by ID
     */
    public function getByID(int $id): ?array {
        $sql = "SELECT * FROM Item WHERE itemID = ?";
        $result = $this->db->query($sql, [$id]);
        
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        
        return null;
    }

    /**
     * Insert new item
     */
    public function insert(array $data): array {
        // Validate required fields
        if (!Validator::hasRequiredKeys($data, ['supplierID', 'itemName', 'itemDescription', 'stockItemQty', 'price'])) {
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        // Sanitize inputs
        $data['itemName'] = Validator::sanitize($data['itemName']);
        $data['itemDescription'] = Validator::sanitize($data['itemDescription']);

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileValidation = Validator::validateFileUpload($_FILES['image']);
            
            if ($fileValidation['success']) {
                $targetDir = __DIR__ . '/../images/';
                $newFileName = "item_" . time() . "." . $fileValidation['type'];
                $targetFile = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $data['ImageFile'] = $newFileName;
                } else {
                    return ['success' => false, 'error' => 'Failed to upload image'];
                }
            }
        }

        // Generate new itemID
        $sql = "SELECT MAX(itemID) AS maxItemId FROM Item";
        $result = $this->db->query($sql);
        $maxItemId = $result['data'][0]['maxItemId'] ?? 0;
        $newItemId = $maxItemId + 1;

        // Prepare SQL with placeholders
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = ?";
            $params[] = $data[$field];
        }

        $sql = "INSERT INTO Item (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', array_fill(0, count($fields), '?')) . ")";

        return $this->db->execute($sql, $params);
    }

    /**
     * Get items by supplier
     */
    public function getBySupplier(string $supplierID): array {
        $sql = "SELECT * FROM Item WHERE supplierID = ? ORDER BY itemName";
        return $this->db->query($sql, [$supplierID])['data'] ?? [];
    }

    /**
     * Check if item exists by name and supplier
     */
    public function existsByName(string $itemName, string $supplierID): bool {
        $sql = "SELECT COUNT(*) AS count FROM Item 
                WHERE itemName = ? AND supplierID = ?";
        $result = $this->db->query($sql, [$itemName, $supplierID]);
        
        return ($result['data'][0]['count'] ?? 0) > 0;
    }

    /**
     * Delete item by ID
     */
    public function delete(int $id): array {
        if (isset($_SESSION['userID']) && $_SESSION['userType'] !== 'manager') {
            return ['success' => false, 'error' => 'Only managers can delete items'];
        }

        $sql = "DELETE FROM Item WHERE itemID = ?";
        return $this->db->execute($sql, [$id]);
    }
}
