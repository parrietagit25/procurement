<?php
// Debug para producción
header('Content-Type: application/json');

echo "=== DEBUG DE PRODUCCIÓN ===\n\n";

// Información del servidor
echo "1. INFORMACIÓN DEL SERVIDOR:\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "\n";
echo "   SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "\n";
echo "   PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'No definido') . "\n";
echo "   QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'No definido') . "\n";
echo "   HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "\n";
echo "   SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "\n";
echo "   HTTPS: " . (isset($_SERVER['HTTPS']) ? 'Sí' : 'No') . "\n";

// Verificar archivos
echo "\n2. ARCHIVOS DEL SISTEMA:\n";
$files = [
    '.htaccess' => 'Configuración Apache',
    'api_router.php' => 'Router API',
    'api/endpoints/admin/dashboard_stats.php' => 'Endpoint dashboard',
    'config/database.php' => 'Configuración BD'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description: $file\n";
    } else {
        echo "   ❌ $description: $file (NO ENCONTRADO)\n";
    }
}

// Verificar .htaccess
echo "\n3. CONTENIDO DE .HTACCESS:\n";
if (file_exists('.htaccess')) {
    echo file_get_contents('.htaccess');
} else {
    echo "❌ Archivo .htaccess no encontrado\n";
}

// Verificar si mod_rewrite está habilitado
echo "\n4. MÓDULOS DE APACHE:\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "   ✅ mod_rewrite está habilitado\n";
    } else {
        echo "   ❌ mod_rewrite NO está habilitado\n";
    }
} else {
    echo "   ⚠️ No se puede verificar mod_rewrite\n";
}

// Probar conexión a base de datos
echo "\n5. CONEXIÓN A BASE DE DATOS:\n";
try {
    require_once 'config/database.php';
    $db = getDB();
    if ($db) {
        echo "   ✅ Conexión a base de datos exitosa\n";
    } else {
        echo "   ❌ Error de conexión a base de datos\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error de conexión: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNÓSTICO COMPLETO ===\n";
?>
