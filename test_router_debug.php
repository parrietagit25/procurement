<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug del Router</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Debug del Router de la API</h1>
    
    <div class="section">
        <h3>Test de Rutas del Router</h3>
        <button onclick="testRoute('/api/orders/1/suppliers', 'POST')">Test POST /api/orders/1/suppliers</button>
        <button onclick="testRoute('/api/orders/1/suppliers/2', 'DELETE')">Test DELETE /api/orders/1/suppliers/2</button>
        <button onclick="testRoute('/api/orders/1', 'GET')">Test GET /api/orders/1</button>
        <button onclick="testRoute('/api/orders', 'GET')">Test GET /api/orders</button>
    </div>
    
    <div id="results"></div>
    
    <script>
        async function testRoute(url, method) {
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> ${method} ${url}</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };
                
                if (method === 'POST') {
                    options.body = JSON.stringify({supplier_ids: [1, 2]});
                }
                
                const response = await fetch(url, options);
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
