<?php
// Sistema de Procurement - Punto de entrada principal
session_start();

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si los archivos de configuración existen
if (!file_exists('config/database.php') || !file_exists('config/config.php')) {
    // Si no existen, redirigir al instalador
    if (file_exists('install.php')) {
        header('Location: install.php');
        exit;
    } else {
        die('Sistema no configurado. Por favor, ejecuta el instalador primero.');
    }
}

// Incluir configuración de base de datos
require_once 'config/database.php';
require_once 'config/config.php';

// Router simple
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Enrutamiento
switch ($path) {
    case '/':
    case '':
        // Redirigir al portal apropiado según el tipo de usuario
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'admin') {
                header('Location: /procurement/admin/dashboard.php');
            } elseif ($_SESSION['user_type'] === 'supplier') {
                header('Location: /procurement/supplier/dashboard.php');
            }
        } else {
            header('Location: /procurement/views/login.php');
        }
        break;
        
    case '/login':
        include 'views/login.php';
        break;
        
    case '/admin':
        include 'admin/dashboard.php';
        break;
        
    case '/supplier':
        include 'supplier/dashboard.php';
        break;
        
    case '/api':
        // API Gateway
        include 'api/index.php';
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 - Página no encontrada</h1>';
        break;
}
?>
