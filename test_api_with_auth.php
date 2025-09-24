<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de la API con Autenticación</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .token-display { background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test de la API con Autenticación</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginAdmin()">Login Admin</button>
        <button onclick="loginSupplier()">Login Supplier</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Test de Endpoints con Token</h3>
        <button onclick="testWithToken('/api/suppliers')">Test /api/suppliers</button>
        <button onclick="testWithToken('/api/orders')">Test /api/orders</button>
        <button onclick="testWithToken('/api/products')">Test /api/products</button>
        <button onclick="testWithToken('/api/categories')">Test /api/categories</button>
        <button onclick="testWithToken('/api/admin/dashboard_stats')">Test /api/admin/dashboard_stats</button>
    </div>
    
    <div id="results"></div>
    
    <script>
        let authToken = null;
        
        async function loginAdmin() {
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Login Admin</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch('/api/login_unified.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'username=admin&password=admin123'
                });
                
                const data = await response.json();
                
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = response.ok ? 'success' : 'error';
                resultDiv2.innerHTML = `
                    <strong>Resultado:</strong> ${response.status} ${response.statusText}<br>
                    <strong>Datos:</strong> <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                resultsDiv.appendChild(resultDiv2);
                
                if (data.success && data.token) {
                    authToken = data.token;
                    document.getElementById('tokenValue').textContent = authToken;
                    document.getElementById('tokenResult').style.display = 'block';
                }
                
            } catch (error) {
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = 'error';
                resultDiv2.innerHTML = `<strong>Error:</strong> ${error.message}`;
                resultsDiv.appendChild(resultDiv2);
            }
        }
        
        async function loginSupplier() {
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Login Supplier</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch('/api/login_unified.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'username=proveedor@abc.com&password=proveedor123'
                });
                
                const data = await response.json();
                
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = response.ok ? 'success' : 'error';
                resultDiv2.innerHTML = `
                    <strong>Resultado:</strong> ${response.status} ${response.statusText}<br>
                    <strong>Datos:</strong> <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                resultsDiv.appendChild(resultDiv2);
                
                if (data.success && data.token) {
                    authToken = data.token;
                    document.getElementById('tokenValue').textContent = authToken;
                    document.getElementById('tokenResult').style.display = 'block';
                }
                
            } catch (error) {
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = 'error';
                resultDiv2.innerHTML = `<strong>Error:</strong> ${error.message}`;
                resultsDiv.appendChild(resultDiv2);
            }
        }
        
        async function testWithToken(endpoint) {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> ${endpoint} (con token)</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(endpoint, {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
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
