<?php
// Test de la API real incluyendo el archivo directamente
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simular la ruta de la API
$_SERVER['REQUEST_URI'] = '/procurement/api/suppliers';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Incluir el archivo de la API
ob_start();
include 'api/index.php';
$output = ob_get_clean();

echo $output;
?>
