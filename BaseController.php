<?php

require_once APP_ROOT . '/config/Database.php';

class BaseController {
    protected $db;

    public function __construct() {
        // Configurar sesión segura
        $this->setupSecureSession();

        // TEMPORAL: Crear sesión de usuario para demostración
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Usuario Demo',
                'email' => 'demo@empresa.com'
            ];
        }

        // Conexión a la base de datos usando el patrón singleton
        $this->db = Database::getInstance()->getConnection();

        // Generar token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function verifyCsrfToken() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('Error de validación CSRF.');
        }
    }

    private function setupSecureSession() {
        if (session_status() == PHP_SESSION_NONE) {
            // Configurar cookies de sesión seguras
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $cookieParams['lifetime'],
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,     // Requiere HTTPS
                'httponly' => true,   // Previene acceso por JavaScript
                'samesite' => 'Strict' // Protección contra CSRF
            ]);
            
            session_start();
            
            // Regenerar ID de sesión periódicamente
            if (!isset($_SESSION['last_regeneration'])) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 3600) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }

        // Generar token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Asegurar que haya un usuario para desarrollo (sin roles)
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = 'Usuario Temporal';
            $_SESSION['user_id'] = 1;
        }
    }
}