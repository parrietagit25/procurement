<?php
// Test de las reglas de reescritura
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$result = [
    'rewrite_test' => [],
    'server_info' => [
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
        'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'Unknown'
    ]
];

// Test 1: Verificar si mod_rewrite está funcionando
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $result['rewrite_test']['mod_rewrite_enabled'] = in_array('mod_rewrite', $modules);
} else {
    $result['rewrite_test']['mod_rewrite_enabled'] = 'Unknown';
}

// Test 2: Verificar si .htaccess está siendo procesado
$htaccess_exists = file_exists('.htaccess');
$result['rewrite_test']['htaccess_exists'] = $htaccess_exists;

if ($htaccess_exists) {
    $htaccess_content = file_get_contents('.htaccess');
    $result['rewrite_test']['htaccess_has_rewrite_engine'] = strpos($htaccess_content, 'RewriteEngine On') !== false;
    $result['rewrite_test']['htaccess_has_api_rule'] = strpos($htaccess_content, '^api/(.*)$') !== false;
}

// Test 3: Verificar si podemos acceder a archivos directamente
$api_file_exists = file_exists('api/index.php');
$result['rewrite_test']['api_file_exists'] = $api_file_exists;

if ($api_file_exists) {
    $api_file_readable = is_readable('api/index.php');
    $result['rewrite_test']['api_file_readable'] = $api_file_readable;
}

// Test 4: Verificar permisos de directorio
$api_dir_writable = is_writable('api/');
$result['rewrite_test']['api_dir_writable'] = $api_dir_writable;

echo json_encode($result, JSON_PRETTY_PRINT);
?>
