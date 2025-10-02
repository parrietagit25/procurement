<?php
// Prueba del router en producción
header('Content-Type: application/json');

echo "=== PRUEBA DEL ROUTER EN PRODUCCIÓN ===\n\n";

// Simular diferentes rutas de API
$test_routes = [
    '/api/admin/dashboard_stats',
    '/api/products',
    '/api/suppliers',
    '/api/orders',
    '/api/categories'
];

echo "1. SIMULANDO RUTAS DE API:\n";
foreach ($test_routes as $route) {
    echo "   Probando: $route\n";
    
    // Simular la lógica del .htaccess
    if (preg_match('/^\/api\/(.*)$/', $route, $matches)) {
        echo "     ✅ Coincide con patrón de API\n";
        echo "     ✅ Debería redirigir a api_router.php\n";
        echo "     ✅ Parámetro: " . $matches[1] . "\n";
    } else {
        echo "     ❌ No coincide con patrón de API\n";
    }
    echo "\n";
}

// Verificar si el router puede procesar las rutas
echo "2. VERIFICANDO ROUTER:\n";
if (file_exists('api_router.php')) {
    echo "   ✅ api_router.php existe\n";
    
    // Simular la lógica del router
    $test_route = '/api/admin/dashboard_stats';
    $path = parse_url($test_route, PHP_URL_PATH);
    
    // Eliminar la ruta base si existe
    $basePath = '/procurement';
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }
    
    // Verificar que sea una ruta de API
    if (strpos($path, '/api/') !== 0) {
        echo "   ❌ No es una ruta de API válida\n";
    } else {
        echo "   ✅ Es una ruta de API válida\n";
        
        // Obtener la ruta relativa de la API (sin /api/)
        $api_path = substr($path, 5); // Remover '/api/'
        echo "   ✅ API Path: " . $api_path . "\n";
        
        // Simular la lógica del router
        $path_parts = explode('/', $api_path);
        $endpoint = $path_parts[0];
        $param = isset($path_parts[1]) ? $path_parts[1] : null;
        
        echo "   ✅ Endpoint: " . $endpoint . "\n";
        echo "   ✅ Param: " . ($param ?? 'null') . "\n";
        
        if ($endpoint === 'admin' && $param === 'dashboard_stats') {
            echo "   ✅ Debería cargar admin/dashboard_stats\n";
        } else {
            echo "   ❌ No coincide con admin/dashboard_stats\n";
        }
    }
} else {
    echo "   ❌ api_router.php NO existe\n";
}

echo "\n3. VERIFICANDO ENDPOINTS:\n";
$endpoints = [
    'api/endpoints/admin/dashboard_stats.php',
    'api/endpoints/products/list.php',
    'api/endpoints/suppliers/list.php',
    'api/endpoints/orders/list.php',
    'api/endpoints/categories/list.php'
];

foreach ($endpoints as $endpoint) {
    if (file_exists($endpoint)) {
        echo "   ✅ $endpoint\n";
    } else {
        echo "   ❌ $endpoint (NO ENCONTRADO)\n";
    }
}

echo "\n=== CONCLUSIÓN ===\n";
echo "Si todos los archivos existen y el router está configurado correctamente,\n";
echo "las rutas de API deberían funcionar. El problema podría ser:\n";
echo "1. El .htaccess no está funcionando correctamente\n";
echo "2. Hay un error en el router que impide el procesamiento\n";
echo "3. Hay un problema de permisos en el servidor\n";
echo "4. Hay un error en la base de datos que impide el funcionamiento\n";

echo "\n=== PRÓXIMOS PASOS ===\n";
echo "1. Probar directamente: https://procurement.grupopcr.com.pa/api/admin/dashboard_stats\n";
echo "2. Si no funciona, probar: https://procurement.grupopcr.com.pa/api/index.php\n";
echo "3. Revisar los logs de error del servidor\n";
echo "4. Verificar que no haya errores de PHP\n";
?>
