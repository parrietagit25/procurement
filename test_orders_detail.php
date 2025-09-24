<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Detalles de Órdenes</title>
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
    <h1>Test de Detalles de Órdenes</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginAdmin()">Login Admin</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Obtener Lista de Órdenes</h3>
        <button onclick="loadOrders()">Cargar Órdenes</button>
        <div id="ordersList"></div>
    </div>
    
    <div class="section">
        <h3>3. Test de Detalles de Órdenes</h3>
        <button onclick="testOrderDetail(1)">Test Orden ID 1</button>
        <button onclick="testOrderDetail(2)">Test Orden ID 2</button>
        <button onclick="testOrderDetail(3)">Test Orden ID 3</button>
    </div>
    
    <div id="results"></div>
    
    <script>
        let authToken = null;
        let orders = [];
        
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
        
        async function loadOrders() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Cargar lista de órdenes</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch('/api/orders', {
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
                
                if (data.success && data.data) {
                    orders = data.data;
                    displayOrdersList();
                }
                
            } catch (error) {
                const resultDiv2 = document.createElement('div');
                resultDiv2.className = 'error';
                resultDiv2.innerHTML = `<strong>Error:</strong> ${error.message}`;
                resultsDiv.appendChild(resultDiv2);
            }
        }
        
        function displayOrdersList() {
            const ordersDiv = document.getElementById('ordersList');
            if (orders.length === 0) {
                ordersDiv.innerHTML = '<p>No hay órdenes disponibles</p>';
                return;
            }
            
            ordersDiv.innerHTML = `
                <h4>Órdenes disponibles:</h4>
                <ul>
                    ${orders.map(order => `
                        <li>
                            <strong>ID:</strong> ${order.id} | 
                            <strong>Número:</strong> ${order.order_number} | 
                            <strong>Título:</strong> ${order.title} | 
                            <strong>Estado:</strong> ${order.status}
                            <button onclick="testOrderDetail(${order.id})" style="margin-left: 10px;">Ver Detalles</button>
                        </li>
                    `).join('')}
                </ul>
            `;
        }
        
        async function testOrderDetail(orderId) {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> /api/orders/${orderId}</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(`/api/orders/${orderId}`, {
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
