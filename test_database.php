<?php
// Test de conexión a la base de datos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$result = [
    'success' => false,
    'message' => '',
    'debug_info' => []
];

try {
    require_once 'config/database.php';
    
    $db = getDB();
    
    if ($db) {
        $result['success'] = true;
        $result['message'] = 'Conexión exitosa a la base de datos';
        
        // Intentar hacer una consulta simple
        $stmt = $db->query("SELECT 1 as test");
        $test_result = $stmt->fetch();
        
        $result['debug_info']['query_test'] = $test_result;
        
        // Verificar si las tablas existen
        $tables = ['users', 'suppliers', 'purchase_orders', 'products', 'categories'];
        $existing_tables = [];
        
        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    $existing_tables[] = $table;
                }
            } catch (Exception $e) {
                $result['debug_info']['table_check_error'] = $e->getMessage();
            }
        }
        
        $result['debug_info']['existing_tables'] = $existing_tables;
        
    } else {
        $result['success'] = false;
        $result['message'] = 'No se pudo conectar a la base de datos';
    }
    
} catch (Exception $e) {
    $result['success'] = false;
    $result['message'] = 'Error: ' . $e->getMessage();
    $result['debug_info']['error_details'] = $e->getTraceAsString();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
