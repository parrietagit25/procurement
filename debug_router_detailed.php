<?php
// Debug detallado del router
header('Content-Type: application/json');

echo "=== DEBUG DETALLADO DEL ROUTER ===\n\n";

// Simular la petición exacta que está fallando
$_SERVER['REQUEST_URI'] = '/procurement/api/admin/dashboard_stats';
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "1. SIMULANDO PETICIÓN:\n";
echo "   REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "   REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";

// Procesar la ruta como lo haría el router
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

echo "\n2. PROCESANDO RUTA:\n";
echo "   Parsed Path: " . $path . "\n";

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

echo "   After removing base: " . $path . "\n";

// Verificar que sea una ruta de API
if (strpos($path, '/api/') !== 0) {
    echo "   ❌ No es una ruta de API válida\n";
    exit;
}

echo "   ✅ Es una ruta de API válida\n";

// Obtener la ruta relativa de la API (sin /api/)
$api_path = substr($path, 5); // Remover '/api/'
echo "   API Path: " . $api_path . "\n";

// Simular la lógica del router
$path_parts = explode('/', $api_path);
$endpoint = $path_parts[0];
$param = isset($path_parts[1]) ? $path_parts[1] : null;

echo "\n3. ANÁLISIS DEL ROUTER:\n";
echo "   Endpoint: " . $endpoint . "\n";
echo "   Param: " . ($param ?? 'null') . "\n";
echo "   Path parts: " . json_encode($path_parts) . "\n";

if ($endpoint === 'admin' && $param === 'dashboard_stats') {
    echo "   ✅ Debería cargar admin/dashboard_stats\n";
} else {
    echo "   ❌ No coincide con admin/dashboard_stats\n";
}

// Verificar si el endpoint existe
$endpoint_file = 'api/endpoints/admin/dashboard_stats.php';
echo "\n4. VERIFICANDO ENDPOINT:\n";
if (file_exists($endpoint_file)) {
    echo "   ✅ Endpoint existe: $endpoint_file\n";
    
    // Verificar si el archivo es legible
    if (is_readable($endpoint_file)) {
        echo "   ✅ Endpoint es legible\n";
    } else {
        echo "   ❌ Endpoint NO es legible\n";
    }
    
    // Verificar el tamaño del archivo
    $file_size = filesize($endpoint_file);
    echo "   ✅ Tamaño del archivo: " . $file_size . " bytes\n";
} else {
    echo "   ❌ Endpoint NO existe: $endpoint_file\n";
}

// Verificar si el router existe
echo "\n5. VERIFICANDO ROUTER:\n";
if (file_exists('api_router.php')) {
    echo "   ✅ api_router.php existe\n";
    
    if (is_readable('api_router.php')) {
        echo "   ✅ api_router.php es legible\n";
    } else {
        echo "   ❌ api_router.php NO es legible\n";
    }
    
    $file_size = filesize('api_router.php');
    echo "   ✅ Tamaño del archivo: " . $file_size . " bytes\n";
} else {
    echo "   ❌ api_router.php NO existe\n";
}

// Probar la conexión a la base de datos
echo "\n6. PROBANDO CONEXIÓN A BASE DE DATOS:\n";
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

echo "\n=== CONCLUSIÓN ===\n";
echo "Si todo está configurado correctamente, el problema podría ser:\n";
echo "1. Un error en el router que impide su ejecución\n";
echo "2. Un error de PHP que no se está mostrando\n";
echo "3. Un problema de permisos en el servidor\n";
echo "4. Un error en la base de datos que impide el funcionamiento\n";

echo "\n=== PRÓXIMOS PASOS ===\n";
echo "1. Probar directamente: https://procurement.grupopcr.com.pa/api/admin/dashboard_stats\n";
echo "2. Si no funciona, revisar los logs de error del servidor\n";
echo "3. Verificar que no haya errores de PHP\n";
echo "4. Probar con el router alternativo: https://procurement.grupopcr.com.pa/api/index.php\n";
?>
