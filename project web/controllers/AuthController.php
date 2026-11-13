<?php
/**
 * Auth Controller
 * Handles user authentication logic (separates from view)
 */

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Validator.php';

class AuthController {
    private Auth $auth;
    
    public function __construct() {
        $this->auth = Auth::getInstance();
    }

    /**
     * Handle login submission
     */
    public function handleLogin(): array {
        // Check if form was submitted
        if (!isset($_POST['login'])) {
            return ['success' => false, 'error' => 'Form not submitted'];
        }

        // Validate inputs
        $errors = [];
        
        if (empty($_POST['userID'])) {
            $errors[] = 'UserID is required';
        }

        if (empty($_POST['password'])) {
            $errors[] = 'Password is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'error' => implode(', ', $errors)];
        }

        // Attempt authentication
        $result = $this->auth->login($_POST['userID'], $_POST['password']);

        if ($result['success']) {
            session_registerror(3600); // Set session lifetime to 1 hour
            
            // Store additional user info in session
            foreach ($result['userData'] as $key => $value) {
                $_SESSION[$key] = $value;
            }

            return [
                'success' => true,
                'redirect' => $result['redirect'],
                'userData' => $result['userData']
            ];
        }

        return ['success' => false, 'error' => $result['error']];
    }

    /**
     * Handle logout
     */
    public function handleLogout(): void {
        $this->auth->logout();
    }

    /**
     * Check authentication status
     */
    public function checkAuth(): array {
        if ($this->auth->isAuthenticated()) {
            return [
                'success' => true,
                'userType' => $this->auth->getUserType(),
                'userId' => $this->auth->getUserId(),
                'username' => $this->auth->getUsername(),
                'userData' => $this->auth->getUserData()
            ];
        }

        return ['success' => false];
    }
}
