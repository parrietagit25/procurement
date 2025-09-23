<?php
// Script de instalación del Sistema de Procurement
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Instalador del Sistema de Procurement</h1>";

// Verificar requisitos
echo "<h2>Verificando Requisitos del Sistema</h2>";

$requirements = [
    'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
    'JSON Extension' => extension_loaded('json'),
    'Mail Function' => function_exists('mail'),
    'Write Permission (uploads/)' => is_writable('uploads/'),
    'Write Permission (config/)' => is_writable('config/')
];

$all_ok = true;
foreach($requirements as $requirement => $status) {
    $status_text = $status ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
    echo "<p>$status_text $requirement</p>";
    if(!$status) $all_ok = false;
}

if(!$all_ok) {
    echo "<p style='color: red;'><strong>Error: No se cumplen todos los requisitos. Por favor, corrige los problemas antes de continuar.</strong></p>";
    exit;
}

echo "<p style='color: green;'><strong>✓ Todos los requisitos se cumplen correctamente.</strong></p>";

// Formulario de configuración
if(!isset($_POST['install'])) {
    ?>
    <h2>Configuración de la Base de Datos</h2>
    <form method="POST">
        <div style="margin: 20px 0;">
            <label>Host de la Base de Datos:</label><br>
            <input type="text" name="db_host" value="localhost" required style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Nombre de la Base de Datos:</label><br>
            <input type="text" name="db_name" value="procurement_system" required style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Usuario de la Base de Datos:</label><br>
            <input type="text" name="db_user" value="root" required style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Contraseña de la Base de Datos:</label><br>
            <input type="password" name="db_pass" style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Clave Secreta JWT:</label><br>
            <input type="text" name="jwt_secret" value="<?php echo bin2hex(random_bytes(32)); ?>" required style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Email del Administrador:</label><br>
            <input type="email" name="admin_email" value="admin@procurement.com" required style="width: 300px; padding: 5px;">
        </div>
        
        <div style="margin: 20px 0;">
            <label>Contraseña del Administrador:</label><br>
            <input type="password" name="admin_password" value="admin123" required style="width: 300px; padding: 5px;">
        </div>
        
        <button type="submit" name="install" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Instalar Sistema
        </button>
    </form>
    <?php
} else {
    // Procesar instalación
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $jwt_secret = $_POST['jwt_secret'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];
    
    try {
        // Conectar a MySQL
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<h2>Creando Base de Datos</h2>";
        
        // Crear base de datos
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p>✓ Base de datos '$db_name' creada exitosamente.</p>";
        
        // Seleccionar base de datos
        $pdo->exec("USE `$db_name`");
        
        // Leer y ejecutar schema
        echo "<h2>Creando Tablas</h2>";
        $schema = file_get_contents('database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach($statements as $statement) {
            $statement = trim($statement);
            if(!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        echo "<p>✓ Tablas creadas exitosamente.</p>";
        
        // Actualizar configuración
        echo "<h2>Configurando Sistema</h2>";
        
        $config_content = "<?php
// Configuración general del sistema
define('SITE_NAME', 'Sistema de Procurement');
define('SITE_URL', 'http://localhost/procurement');
define('ADMIN_EMAIL', '$admin_email');

// Configuración de JWT
define('JWT_SECRET', '$jwt_secret');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600); // 1 hora

// Configuración de archivos
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Estados de órdenes
define('ORDER_STATUS_DRAFT', 'borrador');
define('ORDER_STATUS_SENT', 'enviado');
define('ORDER_STATUS_QUOTED', 'cotizado');
define('ORDER_STATUS_APPROVED', 'aprobado');
define('ORDER_STATUS_IN_PROGRESS', 'en_ejecucion');
define('ORDER_STATUS_RECEIVED', 'recibido');
define('ORDER_STATUS_CANCELLED', 'cancelado');

// Tipos de usuario
define('USER_TYPE_ADMIN', 'admin');
define('USER_TYPE_SUPPLIER', 'supplier');

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu_email@gmail.com');
define('SMTP_PASSWORD', 'tu_password_app');
define('FROM_EMAIL', 'noreply@procurement.com');
define('FROM_NAME', 'Sistema de Procurement');

// Configuración de paginación
define('ITEMS_PER_PAGE', 20);

// Timezone
date_default_timezone_set('America/Mexico_City');
?>";
        
        file_put_contents('config/config.php', $config_content);
        echo "<p>✓ Archivo de configuración actualizado.</p>";
        
        // Actualizar configuración de base de datos
        $db_config_content = "<?php
// Configuración de Base de Datos MySQL
class Database {
    private \$host = '$db_host';
    private \$db_name = '$db_name';
    private \$username = '$db_user';
    private \$password = '$db_pass';
    private \$charset = 'utf8mb4';
    public \$conn;

    public function getConnection() {
        \$this->conn = null;
        
        try {
            \$dsn = \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=\" . \$this->charset;
            \$options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            \$this->conn = new PDO(\$dsn, \$this->username, \$this->password, \$options);
        } catch(PDOException \$exception) {
            echo \"Error de conexión: \" . \$exception->getMessage();
        }
        
        return \$this->conn;
    }
}

// Función helper para obtener conexión
function getDB() {
    \$database = new Database();
    return \$database->getConnection();
}
?>";
        
        file_put_contents('config/database.php', $db_config_content);
        echo "<p>✓ Configuración de base de datos actualizada.</p>";
        
        // Crear usuario administrador
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET email = ?, password_hash = ? WHERE username = 'admin'");
        $stmt->execute([$admin_email, $hashed_password]);
        echo "<p>✓ Usuario administrador configurado.</p>";
        
        // Crear directorio de uploads si no existe
        if(!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        echo "<p>✓ Directorio de uploads creado.</p>";
        
        echo "<h2>¡Instalación Completada!</h2>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>Credenciales de Acceso:</h3>";
        echo "<p><strong>Portal Administrador:</strong></p>";
        echo "<ul>";
        echo "<li>URL: <a href='index.php'>http://localhost/procurement/</a></li>";
        echo "<li>Usuario: admin</li>";
        echo "<li>Contraseña: $admin_password</li>";
        echo "</ul>";
        echo "<p><strong>Portal Proveedores:</strong></p>";
        echo "<ul>";
        echo "<li>URL: <a href='supplier/dashboard.php'>http://localhost/procurement/supplier/</a></li>";
        echo "<li>Email: proveedor@ejemplo.com</li>";
        echo "<li>Contraseña: supplier123</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<p><strong>Próximos pasos:</strong></p>";
        echo "<ul>";
        echo "<li>Configurar el servidor de email en config/config.php</li>";
        echo "<li>Personalizar la configuración según tus necesidades</li>";
        echo "<li>Eliminar este archivo install.php por seguridad</li>";
        echo "<li>Configurar backup automático de la base de datos</li>";
        echo "</ul>";
        
        echo "<p style='color: red;'><strong>IMPORTANTE:</strong> Elimina el archivo install.php después de la instalación por seguridad.</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error durante la instalación: " . $e->getMessage() . "</p>";
    }
}
?>
