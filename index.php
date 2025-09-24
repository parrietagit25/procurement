<?php
// Router principal del sistema de procurement
header('Content-Type: text/html; charset=UTF-8');

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Si la ruta comienza con /api/, redirigir a la API
if (strpos($path, '/api/') === 0) {
    // Simular la ruta de la API para el archivo api/index.php
    $_SERVER['REQUEST_URI'] = $path;
    
    // Incluir el archivo de la API
    ob_start();
    include 'api/index.php';
    $output = ob_get_clean();
    
    // Establecer el content-type correcto para la API
    header('Content-Type: application/json');
    echo $output;
    exit;
}

// Si es la página principal, mostrar el login
if ($path === '/' || $path === '') {
    include 'views/login.php';
    exit;
}

// Para otras rutas, intentar incluir el archivo correspondiente
$file_path = ltrim($path, '/');
if (file_exists($file_path) && is_file($file_path)) {
    include $file_path;
    exit;
}

// Si no se encuentra el archivo, mostrar error 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1>404</h1>
                <h2>Página no encontrada</h2>
                <p>La página que buscas no existe.</p>
                <a href="/" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>