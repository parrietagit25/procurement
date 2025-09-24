<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Todos los Detalles</title>
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
    <h1>Test de Todos los Detalles del Sistema</h1>
    
    <div class="section">
        <h3>1. Obtener Token de Autenticación</h3>
        <button onclick="loginAdmin()">Login Admin</button>
        <div id="tokenResult" class="token-display" style="display: none;">
            <strong>Token:</strong> <span id="tokenValue"></span>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Test de Todos los Endpoints de Detalles</h3>
        <div class="grid">
            <div>
                <h4>Órdenes</h4>
                <button onclick="testOrderDetails()">Test Detalles de Órdenes</button>
                <div id="ordersResult"></div>
            </div>
            <div>
                <h4>Proveedores</h4>
                <button onclick="testSupplierDetails()">Test Detalles de Proveedores</button>
                <div id="suppliersResult"></div>
            </div>
            <div>
                <h4>Productos</h4>
                <button onclick="testProductDetails()">Test Detalles de Productos</button>
                <div id="productsResult"></div>
            </div>
            <div>
                <h4>Cotizaciones</h4>
                <button onclick="testQuotationDetails()">Test Detalles de Cotizaciones</button>
                <div id="quotationsResult"></div>
            </div>
        </div>
    </div>
    
    <div id="results"></div>
    
    <script>
        let authToken = null;
        let testData = {
            orders: [],
            suppliers: [],
            products: [],
            quotations: []
        };
        
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
        
        async function testOrderDetails() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultDiv = document.getElementById('ordersResult');
            resultDiv.innerHTML = '<p>Cargando órdenes...</p>';
            
            try {
                // Cargar lista de órdenes
                const response = await fetch('/api/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    testData.orders = data.data;
                    const firstOrder = data.data[0];
                    
                    // Test detalle de la primera orden
                    const detailResponse = await fetch(`/api/orders/${firstOrder.id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const detailData = await detailResponse.json();
                    
                    resultDiv.innerHTML = `
                        <p><strong>Lista:</strong> ${data.data.length} órdenes encontradas</p>
                        <p><strong>Detalle ID ${firstOrder.id}:</strong> ${detailResponse.ok ? '✅ OK' : '❌ Error'}</p>
                        <pre style="font-size: 12px;">${JSON.stringify(detailData, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.innerHTML = '<p>No hay órdenes disponibles</p>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
            }
        }
        
        async function testSupplierDetails() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultDiv = document.getElementById('suppliersResult');
            resultDiv.innerHTML = '<p>Cargando proveedores...</p>';
            
            try {
                // Cargar lista de proveedores
                const response = await fetch('/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    testData.suppliers = data.data;
                    const firstSupplier = data.data[0];
                    
                    // Test detalle del primer proveedor
                    const detailResponse = await fetch(`/api/suppliers/${firstSupplier.id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const detailData = await detailResponse.json();
                    
                    resultDiv.innerHTML = `
                        <p><strong>Lista:</strong> ${data.data.length} proveedores encontrados</p>
                        <p><strong>Detalle ID ${firstSupplier.id}:</strong> ${detailResponse.ok ? '✅ OK' : '❌ Error'}</p>
                        <pre style="font-size: 12px;">${JSON.stringify(detailData, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.innerHTML = '<p>No hay proveedores disponibles</p>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
            }
        }
        
        async function testProductDetails() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultDiv = document.getElementById('productsResult');
            resultDiv.innerHTML = '<p>Cargando productos...</p>';
            
            try {
                // Cargar lista de productos
                const response = await fetch('/api/products', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    testData.products = data.data;
                    const firstProduct = data.data[0];
                    
                    // Test detalle del primer producto
                    const detailResponse = await fetch(`/api/products/${firstProduct.id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const detailData = await detailResponse.json();
                    
                    resultDiv.innerHTML = `
                        <p><strong>Lista:</strong> ${data.data.length} productos encontrados</p>
                        <p><strong>Detalle ID ${firstProduct.id}:</strong> ${detailResponse.ok ? '✅ OK' : '❌ Error'}</p>
                        <pre style="font-size: 12px;">${JSON.stringify(detailData, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.innerHTML = '<p>No hay productos disponibles</p>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
            }
        }
        
        async function testQuotationDetails() {
            if (!authToken) {
                alert('Primero debes hacer login para obtener un token');
                return;
            }
            
            const resultDiv = document.getElementById('quotationsResult');
            resultDiv.innerHTML = '<p>Cargando cotizaciones...</p>';
            
            try {
                // Cargar lista de cotizaciones
                const response = await fetch('/api/quotations', {
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    testData.quotations = data.data;
                    const firstQuotation = data.data[0];
                    
                    // Test detalle de la primera cotización
                    const detailResponse = await fetch(`/api/quotations/${firstQuotation.id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const detailData = await detailResponse.json();
                    
                    resultDiv.innerHTML = `
                        <p><strong>Lista:</strong> ${data.data.length} cotizaciones encontradas</p>
                        <p><strong>Detalle ID ${firstQuotation.id}:</strong> ${detailResponse.ok ? '✅ OK' : '❌ Error'}</p>
                        <pre style="font-size: 12px;">${JSON.stringify(detailData, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.innerHTML = '<p>No hay cotizaciones disponibles</p>';
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
            }
        }
    </script>
</body>
</html>
