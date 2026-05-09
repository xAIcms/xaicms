<?php

class Csrf {
    public static function generate() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function check($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateOrDie() {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        
        // Also check headers for AJAX requests
        if (empty($token)) {
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                $token = $headers['X-CSRF-Token'] ?? $headers['X-Csrf-Token'] ?? '';
            }
            // Fallback for Nginx/other servers
            if (empty($token)) {
                $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            }
        }

        // Also check JSON input if applicable
        if (empty($token)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['csrf_token'] ?? '';
        }

        if (!self::check($token)) {
            http_response_code(403);
            
            // Check if client expects JSON
            $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
            $isJson = (strpos($accept, 'application/json') !== false) || 
                      (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

            if ($isJson) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'CSRF Token Validation Failed']);
            } else {
                echo 'CSRF Token Validation Failed';
            }
            exit;
        }
    }

    public static function input() {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
