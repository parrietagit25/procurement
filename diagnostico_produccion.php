<?php
// Diagnóstico específico para producción
header('Content-Type: application/json');

echo "=== DIAGNÓSTICO DE PRODUCCIÓN ===\n\n";

// Información del servidor
echo "1. INFORMACIÓN DEL SERVIDOR:\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "\n";
echo "   SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "\n";
echo "   PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'No definido') . "\n";
echo "   HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "\n";
echo "   SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No definido') . "\n";

// Verificar mod_rewrite
echo "\n2. MÓDULOS DE APACHE:\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "   ✅ mod_rewrite está habilitado\n";
    } else {
        echo "   ❌ mod_rewrite NO está habilitado\n";
        echo "   SOLUCIÓN: Habilitar mod_rewrite en el servidor\n";
    }
} else {
    echo "   ⚠️ No se puede verificar mod_rewrite\n";
}

// Verificar .htaccess
echo "\n3. CONFIGURACIÓN .HTACCESS:\n";
if (file_exists('.htaccess')) {
    echo "   ✅ Archivo .htaccess existe\n";
    $htaccess_content = file_get_contents('.htaccess');
    echo "   Contenido:\n";
    echo $htaccess_content . "\n";
} else {
    echo "   ❌ Archivo .htaccess NO existe\n";
    echo "   SOLUCIÓN: Subir el archivo .htaccess al servidor\n";
}

// Verificar archivos del sistema
echo "\n4. ARCHIVOS DEL SISTEMA:\n";
$files = [
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

// Probar ruta API directamente
echo "\n6. PRUEBA DE RUTA API:\n";
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($request_uri, PHP_URL_PATH);

echo "   Ruta solicitada: " . $path . "\n";

if (strpos($path, '/api/') !== false) {
    echo "   ✅ Es una ruta de API\n";
    echo "   Debería ser redirigida a api_router.php\n";
} else {
    echo "   ❌ No es una ruta de API\n";
}

echo "\n=== SOLUCIONES POSIBLES ===\n";
echo "1. Verificar que mod_rewrite esté habilitado en el servidor\n";
echo "2. Verificar que el archivo .htaccess esté en el directorio raíz\n";
echo "3. Verificar que el servidor permita .htaccess\n";
echo "4. Verificar que la base de datos esté configurada correctamente\n";
echo "5. Verificar los permisos de archivos en el servidor\n";
?>
