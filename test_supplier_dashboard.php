<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test del Dashboard del Proveedor</title>
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
    <h1>Test del Dashboard del Proveedor</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginSupplier()">Login Proveedor</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Test de Endpoints del Proveedor</h3>
        <button onclick="testSupplierEndpoints()">Probar Endpoints del Proveedor</button>
        <div id="testResult"></div>
    </div>
    
    <div id="results"></div>
    
    <script>
        let authToken = null;
        
        async function loginSupplier() {
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Login Proveedor</strong>`;
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
        
        async function testSupplierEndpoints() {
            if (!authToken) {
                alert('Primero debes hacer login como proveedor para obtener un token');
                return;
            }
            
            const testDiv = document.getElementById('testResult');
            testDiv.innerHTML = '<p>Probando endpoints del proveedor...</p>';
            
            try {
                // Test dashboard stats
                const statsResponse = await fetch('/api/supplier/dashboard_stats', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const statsData = await statsResponse.json();
                
                // Test orders
                const ordersResponse = await fetch('/api/supplier/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const ordersData = await ordersResponse.json();
                
                testDiv.innerHTML = `
                    <h4>Resultados del Test:</h4>
                    <p><strong>Dashboard Stats:</strong> ${statsResponse.ok ? '✅ OK' : '❌ Error'}</p>
                    <pre style="font-size: 12px;">${JSON.stringify(statsData, null, 2)}</pre>
                    
                    <p><strong>Órdenes del Proveedor:</strong> ${ordersResponse.ok ? '✅ OK' : '❌ Error'}</p>
                    <pre style="font-size: 12px;">${JSON.stringify(ordersData, null, 2)}</pre>
                    
                    ${statsResponse.ok && ordersResponse.ok ? 
                        '<p style="color: green; font-weight: bold;">🎉 ¡Todos los endpoints del proveedor funcionan correctamente!</p>' :
                        '<p style="color: red;">❌ Hay problemas con algunos endpoints del proveedor</p>'
                    }
                `;
                
            } catch (error) {
                testDiv.innerHTML = `<p style="color: red;">Error en el test: ${error.message}</p>`;
            }
        }
    </script>
</body>
</html>
