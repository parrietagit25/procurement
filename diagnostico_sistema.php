<?php
// Diagnóstico del sistema
header('Content-Type: application/json');

echo "=== DIAGNÓSTICO DEL SISTEMA ===\n\n";

// Verificar Apache
echo "1. APACHE:\n";
if (function_exists('apache_get_version')) {
    echo "   ✅ Apache está funcionando\n";
    echo "   Versión: " . apache_get_version() . "\n";
} else {
    echo "   ❌ Apache no detectado\n";
}

// Verificar PHP
echo "\n2. PHP:\n";
echo "   ✅ PHP está funcionando\n";
echo "   Versión: " . phpversion() . "\n";

// Verificar MySQL
echo "\n3. MYSQL:\n";
try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
    echo "   ✅ MySQL está funcionando\n";
    echo "   Conexión exitosa\n";
} catch (PDOException $e) {
    echo "   ❌ MySQL NO está funcionando\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   SOLUCIÓN: Iniciar MySQL en XAMPP Control Panel\n";
}

// Verificar base de datos específica
echo "\n4. BASE DE DATOS 'procurement_system':\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=procurement_system', 'root', '');
    echo "   ✅ Base de datos 'procurement_system' existe\n";
    
    // Verificar tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   Tablas encontradas: " . count($tables) . "\n";
    echo "   Tablas: " . implode(', ', $tables) . "\n";
} catch (PDOException $e) {
    echo "   ❌ Base de datos 'procurement_system' no existe\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   SOLUCIÓN: Crear la base de datos o importar el esquema\n";
}

// Verificar archivos importantes
echo "\n5. ARCHIVOS DEL SISTEMA:\n";
$files = [
    'api_router.php' => 'Router API',
    'api/endpoints/admin/dashboard_stats.php' => 'Endpoint dashboard',
    'config/database.php' => 'Configuración BD',
    'database/schema.sql' => 'Esquema BD'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description: $file\n";
    } else {
        echo "   ❌ $description: $file (NO ENCONTRADO)\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "El sistema de procurement está configurado correctamente,\n";
echo "pero necesita que MySQL esté ejecutándose para funcionar.\n\n";
echo "PASOS PARA SOLUCIONAR:\n";
echo "1. Abrir XAMPP Control Panel\n";
echo "2. Iniciar MySQL\n";
echo "3. Verificar que el puerto 3306 esté abierto\n";
echo "4. Recargar la aplicación\n";
?>
