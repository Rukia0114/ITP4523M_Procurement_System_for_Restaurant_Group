<?php
/**
 * Authentication Service Class
 * Handles user authentication and session management
 */

class Auth {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = Database::getInstance();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Authenticate user by userID and password
     * Supports both Purchase Manager and Supplier roles
     */
    public function login(string $userID, string $password): array {
        $sql = "SELECT * FROM purchasemanager WHERE purchaseManagerID = ? AND password = ? LIMIT 1";
        $result = $this->db->query($sql, [$userID, $password]);

        if ($result['success'] && !empty($result['data'])) {
            return $this->setSession('manager', $result['data'][0]);
        }

        // Try Supplier table
        $sql = "SELECT * FROM Supplier WHERE supplierID = ? AND password = ? LIMIT 1";
        $result = $this->db->query($sql, [$userID, $password]);

        if ($result['success'] && !empty($result['data'])) {
            return $this->setSession('supplier', $result['data'][0]);
        }

        return [
            'success' => false,
            'error' => 'Invalid userID or password'
        ];
    }

    /**
     * Set session data based on user type
     */
    private function setSession(string $type, array $userData): array {
        if (empty($_SESSION)) {
            session_start();
        }

        // Manager session
        if ($type === 'manager') {
            $_SESSION['userID'] = $userData['purchaseManagerID'];
            $_SESSION['managerName'] = $userData['managerName'];
            $_SESSION['contactNumber'] = $userData['contactNumber'];
            $_SESSION['warehouseAddress'] = $userData['warehouseAddress'];
            $_SESSION['userType'] = 'manager';
            
            return [
                'success' => true,
                'redirect' => 'manager/makeOrder.php',
                'userData' => $userData
            ];
        }

        // Supplier session
        if ($type === 'supplier') {
            $_SESSION['userID'] = $userData['supplierID'];
            $_SESSION['companyName'] = $userData['companyName'];
            $_SESSION['contactName'] = $userData['contactName'];
            $_SESSION['contactNumber'] = $userData['contactNumber'];
            $_SESSION['address'] = $userData['address'];
            $_SESSION['userType'] = 'supplier';

            return [
                'success' => true,
                'redirect' => 'supplier/Insert.php',
                'userData' => $userData
            ];
        }

        return ['success' => false, 'error' => 'Unknown user type'];
    }

    /**
     * Logout user
     */
    public function logout(): void {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool {
        return !empty($_SESSION['userID']);
    }

    /**
     * Get current user type
     */
    public function getUserType(): ?string {
        return $_SESSION['userType'] ?? null;
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool {
        return $_SESSION['userType'] === 'manager';
    }

    /**
     * Check if user is supplier
     */
    public function isSupplier(): bool {
        return $_SESSION['userType'] === 'supplier';
    }

    /**
     * Get current user ID
     */
    public function getUserID(): ?string {
        return $_SESSION['userID'] ?? null;
    }

    /**
     * Get username based on user type
     */
    public function getUsername(): ?string {
        if ($this->isManager()) {
            return $_SESSION['managerName'];
        }
        if ($this->isSupplier()) {
            return $_SESSION['companyName'];
        }
        return null;
    }

    /**
     * Get session data based on user type
     */
    public function getUserData(): array {
        $type = $this->getUserType();
        
        if ($type === 'manager') {
            return [
                'userID' => $_SESSION['userID'],
                'name' => $_SESSION['managerName'],
                'contactNumber' => $_SESSION['contactNumber'],
                'warehouseAddress' => $_SESSION['warehouseAddress']
            ];
        } elseif ($type === 'supplier') {
            return [
                'userID' => $_SESSION['userID'],
                'companyName' => $_SESSION['companyName'],
                'contactName' => $_SESSION['contactName'],
                'contactNumber' => $_SESSION['contactNumber'],
                'address' => $_SESSION['address']
            ];
        }

        return [];
    }
}
