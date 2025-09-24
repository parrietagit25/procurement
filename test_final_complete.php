<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Final Completo</title>
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
    <h1>Test Final Completo del Sistema</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginAdmin()">Login Admin</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Test Completo de Asignación de Proveedores</h3>
        <button onclick="runCompleteTest()">Ejecutar Test Completo</button>
        <div id="testResult"></div>
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
        
        async function runCompleteTest() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const testDiv = document.getElementById('testResult');
            testDiv.innerHTML = '<p>Ejecutando test completo...</p>';
            
            try {
                // 1. Cargar órdenes
                const ordersResponse = await fetch('/api/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const ordersData = await ordersResponse.json();
                
                if (!ordersData.success || !ordersData.data || ordersData.data.length === 0) {
                    testDiv.innerHTML = '<p style="color: red;">No hay órdenes disponibles para el test</p>';
                    return;
                }
                
                // 2. Cargar proveedores
                const suppliersResponse = await fetch('/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const suppliersData = await suppliersResponse.json();
                
                if (!suppliersData.success || !suppliersData.data || suppliersData.data.length === 0) {
                    testDiv.innerHTML = '<p style="color: red;">No hay proveedores disponibles para el test</p>';
                    return;
                }
                
                const order = ordersData.data[0];
                const supplier = suppliersData.data[0];
                
                testDiv.innerHTML = `
                    <p><strong>Orden seleccionada:</strong> ID ${order.id} - ${order.title}</p>
                    <p><strong>Proveedor seleccionado:</strong> ID ${supplier.id} - ${supplier.company_name}</p>
                    <p>Probando asignación...</p>
                `;
                
                // 3. Test de asignación
                const assignResponse = await fetch(`/api/orders/${order.id}/suppliers`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({supplier_ids: [supplier.id]})
                });
                
                const assignData = await assignResponse.json();
                
                if (assignResponse.ok && assignData.success) {
                    testDiv.innerHTML += `
                        <p style="color: green;">✅ Asignación exitosa: ${assignData.message}</p>
                        <p>Probando eliminación...</p>
                    `;
                    
                    // 4. Test de eliminación
                    const removeResponse = await fetch(`/api/orders/${order.id}/suppliers/${supplier.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const removeData = await removeResponse.json();
                    
                    if (removeResponse.ok && removeData.success) {
                        testDiv.innerHTML += `
                            <p style="color: green;">✅ Eliminación exitosa: ${removeData.message}</p>
                            <p style="color: green; font-weight: bold;">🎉 ¡Test completo exitoso! El sistema funciona correctamente.</p>
                        `;
                    } else {
                        testDiv.innerHTML += `
                            <p style="color: red;">❌ Error en eliminación: ${removeData.error || removeData.message}</p>
                            <p style="color: orange;">⚠️ La asignación funcionó, pero hay un problema con la eliminación</p>
                        `;
                    }
                } else {
                    testDiv.innerHTML += `
                        <p style="color: red;">❌ Error en asignación: ${assignData.error || assignData.message}</p>
                        <p style="color: red;">El error específico es: ${JSON.stringify(assignData, null, 2)}</p>
                    `;
                }
                
            } catch (error) {
                testDiv.innerHTML = `<p style="color: red;">Error en el test: ${error.message}</p>`;
            }
        }
    </script>
</body>
</html>
