<?php
// Prueba específica de rutas de API en producción
header('Content-Type: application/json');

echo "=== PRUEBA DE RUTAS DE API EN PRODUCCIÓN ===\n\n";

// Información del servidor
echo "1. INFORMACIÓN ACTUAL:\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "\n";
echo "   SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "\n";
echo "   HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "\n";

// Verificar si estamos en una ruta de API
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($request_uri, PHP_URL_PATH);

echo "\n2. ANÁLISIS DE RUTA:\n";
echo "   Ruta solicitada: " . $path . "\n";

if (strpos($path, '/api/') !== false) {
    echo "   ✅ Es una ruta de API\n";
    echo "   Debería ser redirigida a api_router.php\n";
} else {
    echo "   ❌ No es una ruta de API\n";
    echo "   Para probar, accede a: https://procurement.grupopcr.com.pa/api/admin/dashboard_stats\n";
}

// Verificar archivos críticos
echo "\n3. ARCHIVOS CRÍTICOS:\n";
$files = [
    'api_router.php' => 'Router principal',
    'api/index.php' => 'Router alternativo',
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

// Probar conexión a base de datos
echo "\n4. CONEXIÓN A BASE DE DATOS:\n";
try {
    require_once 'config/database.php';
    $db = getDB();
    if ($db) {
        echo "   ✅ Conexión a base de datos exitosa\n";
        
        // Probar una consulta simple
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch();
        echo "   ✅ Consulta de prueba exitosa\n";
        echo "   Productos en BD: " . $result['count'] . "\n";
    } else {
        echo "   ❌ Error de conexión a base de datos\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error de conexión: " . $e->getMessage() . "\n";
}

echo "\n=== INSTRUCCIONES PARA PROBAR ===\n";
echo "1. Accede a: https://procurement.grupopcr.com.pa/api/admin/dashboard_stats\n";
echo "2. Debería mostrar estadísticas JSON o error de autenticación\n";
echo "3. Si muestra 'Endpoint no encontrado', hay un problema con el router\n";
echo "4. Si muestra error de autenticación, el router funciona correctamente\n";

echo "\n=== RUTAS DE PRUEBA ===\n";
echo "✅ https://procurement.grupopcr.com.pa/api/products\n";
echo "✅ https://procurement.grupopcr.com.pa/api/suppliers\n";
echo "✅ https://procurement.grupopcr.com.pa/api/orders\n";
echo "✅ https://procurement.grupopcr.com.pa/api/categories\n";
echo "✅ https://procurement.grupopcr.com.pa/api/admin/dashboard_stats\n";
?>
