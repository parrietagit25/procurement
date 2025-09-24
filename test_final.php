<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Final del Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test Final del Sistema de Procurement</h1>
    
    <div id="results"></div>
    
    <button onclick="testEndpoint('/api/suppliers')">Test /api/suppliers</button>
    <button onclick="testEndpoint('/api/orders')">Test /api/orders</button>
    <button onclick="testEndpoint('/api/products')">Test /api/products</button>
    <button onclick="testEndpoint('/api/categories')">Test /api/categories</button>
    <button onclick="testEndpoint('/api/admin/dashboard_stats')">Test /api/admin/dashboard_stats</button>
    
    <script>
        async function testEndpoint(endpoint) {
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> ${endpoint}`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(endpoint);
                const data = await response.json();
                
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = response.ok ? 'success' : 'error';
                resultDiv2.innerHTML = `
                    <strong>Resultado:</strong> ${response.status} ${response.statusText}<br>
                    <strong>Datos:</strong> <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                resultsDiv.appendChild(resultDiv2);
                
            } catch (error) {
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = 'error';
                resultDiv2.innerHTML = `<strong>Error:</strong> ${error.message}`;
                resultsDiv.appendChild(resultDiv2);
            }
        }
    </script>
</body>
</html>
