<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Orden de Compra - Sistema de Procurement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .item-row {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .supplier-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .supplier-card:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        .supplier-card.selected {
            border-color: #667eea;
            background: #e3f2fd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Procurement</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="create_order.php">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Orden
                        </a>
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-file-invoice me-2"></i>Órdenes
                        </a>
                        <a class="nav-link" href="suppliers.php">
                            <i class="fas fa-truck me-2"></i>Proveedores
                        </a>
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box me-2"></i>Productos
                        </a>
                        <a class="nav-link" href="quotations.php">
                            <i class="fas fa-calculator me-2"></i>Cotizaciones
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-plus-circle me-2"></i>Crear Orden de Compra</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Bienvenido, <strong id="userName">Usuario</strong></span>
                            <button class="btn btn-outline-danger btn-sm" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </button>
                        </div>
                    </div>
                    
                    <!-- Formulario de Orden -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-file-invoice me-2"></i>Información de la Orden</h5>
                        </div>
                        <div class="card-body">
                            <form id="orderForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Título de la Orden *</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Prioridad *</label>
                                            <select class="form-select" id="priority" name="priority" required>
                                                <option value="low">Baja</option>
                                                <option value="medium" selected>Media</option>
                                                <option value="high">Alta</option>
                                                <option value="urgent">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Departamento</label>
                                            <input type="text" class="form-control" id="department" name="department">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="required_date" class="form-label">Fecha Requerida</label>
                                            <input type="date" class="form-control" id="required_date" name="required_date">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Items de la Orden -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-list me-2"></i>Items de la Orden</h5>
                            <button class="btn btn-primary btn-sm" onclick="addItem()">
                                <i class="fas fa-plus me-1"></i>Agregar Item
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="itemsContainer">
                                <!-- Los items se agregarán dinámicamente -->
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6>Total Estimado: $<span id="totalAmount">0.00</span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selección de Proveedores 
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-truck me-2"></i>Proveedores a Invitar</h5>
                            <button class="btn btn-primary btn-sm" onclick="loadSuppliers()">
                                <i class="fas fa-sync me-1"></i>Actualizar Lista
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="suppliersContainer">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando proveedores...
                                </div>
                            </div>
                        </div>
                    </div>-->
                    
                    <!-- Botones de Acción -->
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <button class="btn btn-success btn-lg me-3" onclick="saveOrder()">
                                <i class="fas fa-save me-2"></i>Guardar Orden
                            </button>
                            <button class="btn btn-primary btn-lg" onclick="saveAndSend()">
                                <i class="fas fa-paper-plane me-2"></i>Guardar y Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let itemCount = 0;
        let selectedSuppliers = [];
        let userData = null;
        
        // Verificar autenticación
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            const userType = localStorage.getItem('userType');
            
            if(!token || userType !== 'admin') {
                window.location.href = '/procurement/views/login.php';
                return;
            }
            
            userData = JSON.parse(localStorage.getItem('userData'));
            document.getElementById('userName').textContent = userData.first_name + ' ' + userData.last_name;
            
            // Cargar proveedores
            loadSuppliers();
        });
        
        // Agregar item
        function addItem() {
            itemCount++;
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
                <div class="item-row" id="item-${itemCount}">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Producto/Servicio *</label>
                            <input type="text" class="form-control" name="item_name_${itemCount}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" name="quantity_${itemCount}" step="0.01" min="0" required onchange="calculateTotal()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unidad *</label>
                            <select class="form-select" name="unit_${itemCount}" required>
                                <option value="pieza">Pieza</option>
                                <option value="kg">Kilogramo</option>
                                <option value="litro">Litro</option>
                                <option value="metro">Metro</option>
                                <option value="hora">Hora</option>
                                <option value="servicio">Servicio</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Precio Est.</label>
                            <input type="number" class="form-control" name="estimated_price_${itemCount}" step="0.01" min="0" onchange="calculateTotal()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="number" class="form-control" name="total_${itemCount}" step="0.01" readonly>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeItem(${itemCount})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description_${itemCount}" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        }
        
        // Remover item
        function removeItem(itemId) {
            document.getElementById(`item-${itemId}`).remove();
            calculateTotal();
        }
        
        // Calcular total
        function calculateTotal() {
            let total = 0;
            for(let i = 1; i <= itemCount; i++) {
                const item = document.getElementById(`item-${i}`);
                if(item) {
                    const quantity = parseFloat(item.querySelector(`[name="quantity_${i}"]`).value) || 0;
                    const price = parseFloat(item.querySelector(`[name="estimated_price_${i}"]`).value) || 0;
                    const itemTotal = quantity * price;
                    
                    item.querySelector(`[name="total_${i}"]`).value = itemTotal.toFixed(2);
                    total += itemTotal;
                }
            }
            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }
        
        // Cargar proveedores
        async function loadSuppliers() {
            try {
                const response = await fetch('/procurement/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    displaySuppliers(result.data);
                } else {
                    document.getElementById('suppliersContainer').innerHTML = 
                        '<div class="alert alert-warning">No se pudieron cargar los proveedores</div>';
                }
            } catch(error) {
                console.error('Error loading suppliers:', error);
                document.getElementById('suppliersContainer').innerHTML = 
                    '<div class="alert alert-danger">Error al cargar proveedores</div>';
            }
        }
        
        // Mostrar proveedores
        function displaySuppliers(suppliers) {
            const container = document.getElementById('suppliersContainer');
            
            if(suppliers.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No hay proveedores disponibles</div>';
                return;
            }
            
            container.innerHTML = suppliers.map(supplier => `
                <div class="supplier-card" onclick="toggleSupplier(${supplier.id})" id="supplier-${supplier.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${supplier.company_name}</h6>
                            <small class="text-muted">${supplier.contact_name} - ${supplier.email}</small>
                        </div>
                        <div>
                            <span class="badge bg-${supplier.status === 'approved' ? 'success' : 'warning'}">${supplier.status}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Toggle proveedor seleccionado
        function toggleSupplier(supplierId) {
            const card = document.getElementById(`supplier-${supplierId}`);
            const index = selectedSuppliers.indexOf(supplierId);
            
            if(index > -1) {
                selectedSuppliers.splice(index, 1);
                card.classList.remove('selected');
            } else {
                selectedSuppliers.push(supplierId);
                card.classList.add('selected');
            }
        }
        
        // Guardar orden
        async function saveOrder() {
            if(!validateForm()) return;
            
            const orderData = collectOrderData();
            orderData.status = 'borrador';
            
            try {
                const response = await fetch('/procurement/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Orden guardada exitosamente');
                    window.location.href = 'orders.php';
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error saving order:', error);
                alert('Error al guardar la orden');
            }
        }
        
        // Guardar y enviar
        async function saveAndSend() {
            if(!validateForm()) return;
            
            const orderData = collectOrderData();
            orderData.status = 'enviado';
            
            try {
                const response = await fetch('/procurement/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    // Asignar proveedores
                    if(selectedSuppliers.length > 0) {
                        await assignSuppliers(result.order_id);
                    }
                    
                    alert('Orden creada y enviada exitosamente');
                    window.location.href = 'orders.php';
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error saving order:', error);
                alert('Error al guardar la orden');
            }
        }
        
        // Asignar proveedores
        async function assignSuppliers(orderId) {
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}/suppliers`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify({supplier_ids: selectedSuppliers})
                });
                
                const result = await response.json();
                if(!result.success) {
                    console.error('Error assigning suppliers:', result.message);
                }
            } catch(error) {
                console.error('Error assigning suppliers:', error);
            }
        }
        
        // Validar formulario
        function validateForm() {
            const title = document.getElementById('title').value;
            if(!title.trim()) {
                alert('El título de la orden es requerido');
                return false;
            }
            
            if(itemCount === 0) {
                alert('Debe agregar al menos un item a la orden');
                return false;
            }
            
            return true;
        }
        
        // Recopilar datos del formulario
        function collectOrderData() {
            const formData = new FormData(document.getElementById('orderForm'));
            const data = Object.fromEntries(formData);
            
            // Recopilar items
            const items = [];
            for(let i = 1; i <= itemCount; i++) {
                const item = document.getElementById(`item-${i}`);
                if(item) {
                    const itemData = {
                        product_name: item.querySelector(`[name="item_name_${i}"]`).value,
                        description: item.querySelector(`[name="description_${i}"]`).value,
                        quantity: parseFloat(item.querySelector(`[name="quantity_${i}"]`).value),
                        unit: item.querySelector(`[name="unit_${i}"]`).value,
                        estimated_price: parseFloat(item.querySelector(`[name="estimated_price_${i}"]`).value) || 0,
                        total_price: parseFloat(item.querySelector(`[name="total_${i}"]`).value) || 0
                    };
                    items.push(itemData);
                }
            }
            
            data.items = items;
            data.total_amount = parseFloat(document.getElementById('totalAmount').textContent);
            
            return data;
        }
        
        // Cerrar sesión
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('userData');
            localStorage.removeItem('userType');
            window.location.href = '/procurement/views/login.php';
        }
    </script>
</body>
</html>
