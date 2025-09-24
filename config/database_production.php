<?php
// Configuración de Base de Datos MySQL para Producción
class Database {
    private $host = 'localhost'; // Cambiar por la IP del servidor de base de datos
    private $db_name = 'procurement_system';
    private $username = 'root'; // Cambiar por el usuario de la base de datos
    private $password = ''; // Cambiar por la contraseña de la base de datos
    private $charset = 'utf8mb4';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Error de conexión a la base de datos: " . $exception->getMessage());
            // En lugar de mostrar el error, devolver null para manejo posterior
            return null;
        }
        
        return $this->conn;
    }
}

// Función helper para obtener conexión
function getDB() {
    $database = new Database();
    return $database->getConnection();
}
?>
