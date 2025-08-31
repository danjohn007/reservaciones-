<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'ejercito_aid';
    private $username = 'ejercito_aid';
    private $password = 'Danjohn007!';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Función para obtener la conexión a la base de datos
function getDB() {
    $database = new Database();
    return $database->getConnection();
}

// Configuración de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir rutas base
define('BASE_URL', 'https://ejercitodigital.com.mx/aid/public/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Función helper para generar URLs completas
function getUrl($path = '') {
    $baseUrl = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $baseUrl . ($path ? '/' . $path : '');
}

// Crear directorio de uploads si no existe
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
