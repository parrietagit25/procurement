<?php
// Test directo del router de la API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Directo del Router de la API</h1>";

// Simular una petición a /api/suppliers
$_SERVER['REQUEST_URI'] = '/procurement/api/suppliers';
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "<h2>Simulando petición a /api/suppliers</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>REQUEST_METHOD:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";

// Capturar la salida del router
ob_start();
include 'api_router.php';
$output = ob_get_clean();

echo "<h3>Respuesta del Router:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Test de la base de datos
echo "<h2>Test de Conexión a la Base de Datos</h2>";
try {
    require_once 'config/database.php';
    $db = getDB();
    if ($db) {
        echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa</p>";
        
        // Test de la clase Supplier
        require_once 'classes/Supplier.php';
        $supplier = new Supplier($db);
        $suppliers = $supplier->getAll(5, 0);
        
        echo "<p style='color: green;'>✅ Clase Supplier funciona correctamente</p>";
        echo "<p><strong>Proveedores encontrados:</strong> " . count($suppliers) . "</p>";
        
        if (count($suppliers) > 0) {
            echo "<h4>Primer proveedor:</h4>";
            echo "<pre>" . json_encode($suppliers[0], JSON_PRETTY_PRINT) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error de conexión a la base de datos</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
