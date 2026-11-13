<?php
/**
 * Error Handler Class
 * Centralized error handling and logging
 */

class ErrorHandler {
    private static $instance = null;
    private $errorLevels = [E_ERROR, E_WARNING, E_PARSE, E_NOTICE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE];
    private $logFile;

    private function __construct() {
        $config = require __DIR__ . '/../config/app.php';
        $this->logFile = __DIR__ . '/../logs/error.log';
        
        // Ensure logs directory exists
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0755, true);
        }

        // Error logging (but not in production debug mode)
        if (!$config['debug']) {
            set_error_handler([$this, 'handleError']);
            register_shutdown_function([$this, 'writeFatalErrorToLog']);
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Handle PHP errors
     */
    public function handleError($errno, $errstr, $errfile, $errline): bool {
        if (($errno & $this->errorLevels[0]) !== 0) {
            $this->logError($errno, $errstr, $errfile, $errline);
            
            if ($errno === E_ERROR || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR) {
                $message = 'A Fatal Error has occurred';
                exit($message);
            }
        }

        return false;
    }

    /**
     * Log error to file
     */
    private function logError(int $errno, string $errstr, string $errfile, int $errline): void {
        if (!$config = null && isset($config['debug']) && !$config['debug']) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $errorLevel = [
            E_ERROR => 'FATAL ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE'
        ][$errno] ?? 'UNKNOWN';

        $logMessage = "[{$timestamp}] {$errorLevel}: {$errstr} in {$errfile} on line {$errline}\n";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Write fatal error to log on shutdown
     */
    public function writeFatalErrorToLog(): void {
        $lastError = error_get_last();
        
        if ($lastError && in_array($lastError['type'], [E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->logError($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
        }
    }

    /**
     * Handle exceptions
     */
    public function handleException(Exception $e): void {
        if (!$config = null && isset($config['debug']) && !$config['debug']) {
            $message = 'An error occurred';
            header('Status: 500 Internal Server Error');
            echo $message;
            return;
        }

        $this->logError(E_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());

        if ($config = null && isset($config['debug'])) {
            echo '<pre>';
            echo 'Type: ' . get_class($e) . "\n";
            echo 'Message: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine();
            echo 'Stack trace:\n' . $e->getTraceAsString();
            echo '</pre>';
        } else {
            echo 'An unexpected error occurred. Please contact administrator.';
        }
    }

    /**
     * Set custom error handler
     */
    public function setAsErrorHandler(): void {
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'writeFatalErrorToLog']);
    }

    /**
     * Get recent errors from log
     */
    public function getRecentErrors(int $limit = 10): array {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errors = array_slice(array_reverse($lines), 0, $limit, true);
        
        return array_map(function($error) {
            return $error;
        }, $errors);
    }
}
