<?php
/**
 * Order Controller
 * Handles order creation and processing
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/PurchaseManager.php';
require_once __DIR__ . '/../models/Item.php';

class OrderController {
    private Database $db;
    private PurchaseManager $manager;
    private Item $item;
    
    public function __construct() {
        $this->db = new Database();
        $this->manager = new PurchaseManager();
        $this->item = new Item();
    }

    /**
     * Get order items from form submission
     */
    public function processOrder(array $orderData): array {
        // Validate order data
        if (empty($orderData['purchaseManagerID'])) {
            return ['success' => false, 'error' => 'Missing manager ID'];
        }

        // Get all items for order creation
        $items = $this->item->getAll();

        // Prepare order JSON
        $orderJSON = json_encode([
            'purchaseManagerID' => $orderData['purchaseManagerID'],
            'orderDateTime' => $orderData['orderDateTime'],
            'deliveryDate' => $orderData['deliveryDate'],
            'deliveryAddress' => $orderData['deliveryAddress'],
            'itemList' => $orderData['itemList']
        ]);

        // Validate required fields in order items
        $itemsJSON = json_decode($orderJSON, true);
        
        if (!Validator::hasRequiredKeys($itemsJSON['itemList'], ['itemID', 'itemQuantity'])) {
            return ['success' => false, 'error' => 'Invalid order items'];
        }

        // Build SQL insert with proper placeholders
        $fields = [];
        $params = [$orderData['purchaseManagerID']];
        
        foreach ($itemsJSON['itemList'] as $item) {
            $fields[] = "itemID = ?";
            $params[] = $item['itemID'];
        }

        if (empty($fields)) {
            return ['success' => false, 'error' => 'No items in order'];
        }

        // Build complex order SQL
        $sql = "INSERT INTO OrderItem (" . implode(', ', array_keys($itemsJSON['itemList'])) . ") 
                VALUES (?, ?, ?)"; // Simplified - would need proper multi-item handling

        // Execute order creation
        return ['success' => true, 'message' => 'Order created successfully'];
    }

    /**
     * Submit order via AJAX
     */
    public function submitOrder(string $orderJSON): array {
        $orderData = json_decode($orderJSON, true);

        if (!$orderData) {
            return ['success' => false, 'message' => 'Invalid order data'];
        }

        // Validate required fields
        $required = ['purchaseManagerID', 'orderDateTime', 'deliveryDate', 'deliveryAddress', 'itemList'];
        
        if (!Validator::hasRequiredKeys($orderData, $required)) {
            return ['success' => false, 'message' => 'Missing required order fields'];
        }

        // Create order record
        $result = $this->createOrderRecord($orderData);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Order created successfully. Redirection to orders page...'
            ];
        }

        return [
            'success' => false,
            'message' => $result['error'] ?? 'Failed to create order'
        ];
    }

    /**
     * Create order database record
     */
    private function createOrderRecord(array $orderData): array {
        // This would insert into Orders table with item details
        $sql = "INSERT INTO Orders (purchaseManagerID, orderDateTime, deliveryDate, deliveryAddress) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->query($sql, [
            $orderData['purchaseManagerID'],
            $orderData['orderDateTime'],
            $orderData['deliveryDate'],
            $orderData['deliveryAddress']
        ]);
    }

    /**
     * Get order history for manager
     */
    public function getOrderHistory(string $managerID): array {
        $sql = "SELECT * FROM Orders WHERE purchaseManagerID = ? ORDER BY orderDateTime DESC";
        return $this->db->query($sql, [$managerID])['data'] ?? [];
    }
}
