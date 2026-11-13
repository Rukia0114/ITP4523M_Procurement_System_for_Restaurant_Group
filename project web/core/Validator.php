<?php
/**
 * Validation Service Class
 * Handles input validation and sanitization
 */

class Validator {
    
    /**
     * Validate email address
     */
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate string is not empty/whitespace only
     */
    public static function notEmpty(string $value): bool {
        return trim($value) !== '';
    }

    /**
     * Validate number is within range
     */
    public static function numberBetween(int $value, int $min, int $max): bool {
        return $value >= $min && $value <= $max;
    }

    /**
     * Validate positive number
     */
    public static function positiveNumber(float $value): bool {
        return $value > 0;
    }

    /**
     * Sanitize string input
     */
    public static function sanitize(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate array contains required keys with values
     */
    public static function hasRequiredKeys(array $data, array $requiredKeys): bool {
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data) || empty($data[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload(array $fileInfo): array {
        if ($fileInfo['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            
            // Check against config settings
            $config = require __DIR__ . '/../config/app.php';
            
            if (in_array($extension, $config['allowed_image_extensions'])) {
                if ($fileInfo['size'] <= $config['max_upload_size']) {
                    return [
                        'success' => true,
                        'filename' => basename($fileInfo['name']),
                        'type' => $extension
                    ];
                }
            }
        }

        return [
            'success' => false,
            'error' => [
                UPLOAD_ERR_INI_SIZE => 'File is too large',
                UPLOAD_ERR_FORM_SIZE => 'Uploaded file exceeds the maximum allowed size',
                UPLOAD_ERR_PARTIAL => 'File upload was only partially successful',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
            ][$fileInfo['error']] ?? 'Unknown upload error'
        ];
    }

    /**
     * Validate date format and range
     */
    public static function validateDate(string $date, ?string $minDate = null): array {
        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $date);
        
        if ($dt === false || $dt === null) {
            return ['success' => false, 'error' => 'Invalid date format'];
        }

        if ($minDate && strtotime($date) < strtotime($minDate)) {
            return ['success' => false, 'error' => 'Date is in the past'];
        }

        return ['success' => true, 'datetime' => $date];
    }

    /**
     * Validate array values are unique
     */
    public static function areUnique(array $values): bool {
        return count($values) === count(array_unique($values));
    }

    /**
     * Generate error message for validation failure
     */
    public static function getErrorMessage(array $errors): string {
        if (empty($errors)) {
            return '';
        }
        
        $messages = [];
        foreach ($errors as $field => $error) {
            if (!is_array($error)) {
                $error = [$error];
            }
            foreach ($error as $msg) {
                $messages[] = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            }
        }

        return implode(', ', $messages);
    }
}
