<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Asignación de Proveedores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .token-display { background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    </style>
</head>
<body>
    <h1>Test de Asignación de Proveedores a Órdenes</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginAdmin()">Login Admin</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Cargar Datos</h3>
        <button onclick="loadData()">Cargar Órdenes y Proveedores</button>
        <div id="dataResult"></div>
    </div>
    
    <div class="section">
        <h3>3. Test de Asignación de Proveedores</h3>
        <div id="assignmentResult"></div>
    </div>
    
    <div id="results"></div>
    
    <script>
        let authToken = null;
        let orders = [];
        let suppliers = [];
        
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
        
        async function loadData() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const dataDiv = document.getElementById('dataResult');
            dataDiv.innerHTML = '<p>Cargando datos...</p>';
            
            try {
                // Cargar órdenes
                const ordersResponse = await fetch('/api/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const ordersData = await ordersResponse.json();
                
                // Cargar proveedores
                const suppliersResponse = await fetch('/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const suppliersData = await suppliersResponse.json();
                
                if (ordersData.success && suppliersData.success) {
                    orders = ordersData.data;
                    suppliers = suppliersData.data;
                    
                    dataDiv.innerHTML = `
                        <p><strong>Órdenes cargadas:</strong> ${orders.length}</p>
                        <p><strong>Proveedores cargados:</strong> ${suppliers.length}</p>
                        <div class="grid">
                            <div>
                                <h4>Órdenes disponibles:</h4>
                                <ul>
                                    ${orders.map(order => `<li>ID: ${order.id} - ${order.title}</li>`).join('')}
                                </ul>
                            </div>
                            <div>
                                <h4>Proveedores disponibles:</h4>
                                <ul>
                                    ${suppliers.map(supplier => `<li>ID: ${supplier.id} - ${supplier.company_name}</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                    
                    // Mostrar botones de test
                    showAssignmentTests();
                } else {
                    dataDiv.innerHTML = '<p style="color: red;">Error al cargar datos</p>';
                }
                
            } catch (error) {
                dataDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
            }
        }
        
        function showAssignmentTests() {
            const assignmentDiv = document.getElementById('assignmentResult');
            
            if (orders.length > 0 && suppliers.length > 0) {
                const firstOrder = orders[0];
                const firstSupplier = suppliers[0];
                
                assignmentDiv.innerHTML = `
                    <h4>Test de Asignación</h4>
                    <p><strong>Orden:</strong> ID ${firstOrder.id} - ${firstOrder.title}</p>
                    <p><strong>Proveedor:</strong> ID ${firstSupplier.id} - ${firstSupplier.company_name}</p>
                    <button onclick="testAssignSupplier(${firstOrder.id}, ${firstSupplier.id})">
                        Asignar Proveedor a Orden
                    </button>
                    <button onclick="testRemoveSupplier(${firstOrder.id}, ${firstSupplier.id})">
                        Remover Proveedor de Orden
                    </button>
                `;
            } else {
                assignmentDiv.innerHTML = '<p>No hay datos suficientes para realizar el test</p>';
            }
        }
        
        async function testAssignSupplier(orderId, supplierId) {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Asignar proveedor ${supplierId} a orden ${orderId}</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(`/api/orders/${orderId}/suppliers`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({supplier_ids: [supplierId]})
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
        
        async function testRemoveSupplier(orderId, supplierId) {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultsDiv = document.getElementById('results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'info';
            resultDiv.innerHTML = `<strong>Probando:</strong> Remover proveedor ${supplierId} de orden ${orderId}</strong>`;
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(`/api/orders/${orderId}/suppliers/${supplierId}`, {
                    method: 'DELETE',
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
