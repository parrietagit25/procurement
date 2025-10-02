<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Proveedor - Sistema de Procurement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .supplier-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .order-card {
            border-left: 4px solid #28a745;
        }
        .order-card.pending {
            border-left-color: #ffc107;
        }
        .order-card.quoted {
            border-left-color: #17a2b8;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
</head>
<body>
    <div class="supplier-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-truck me-2"></i>Portal del Proveedor</h2>
                    <p class="mb-0">Bienvenido, <strong id="companyName">Empresa</strong></p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-outline-light" onclick="logout()">
                        <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number" id="totalOrders">0</div>
                        <div class="stat-label">Órdenes Asignadas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number" id="pendingQuotations">0</div>
                        <div class="stat-label">Cotizaciones Pendientes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number" id="quotedOrders">0</div>
                        <div class="stat-label">Cotizadas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number" id="approvedOrders">0</div>
                        <div class="stat-label">Aprobadas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill">
                            <li class="nav-item">
                                <a class="nav-link active" href="#" data-section="orders">
                                    <i class="fas fa-file-invoice me-2"></i>Mis Órdenes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="quotations">
                                    <i class="fas fa-calculator me-2"></i>Cotizaciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="deliveries">
                                    <i class="fas fa-shipping-fast me-2"></i>Entregas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="invoices">
                                    <i class="fas fa-receipt me-2"></i>Facturas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="profile">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div id="mainContent">
            <!-- Órdenes Asignadas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-file-invoice me-2"></i>Órdenes Asignadas</h5>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-success btn-sm" onclick="filterOrders('all')">Todas</button>
                        <button class="btn btn-outline-warning btn-sm" onclick="filterOrders('pending')">Pendientes</button>
                        <button class="btn btn-outline-info btn-sm" onclick="filterOrders('quoted')">Cotizadas</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Título</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Monto</th>
                                    <th>Fecha Límite</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTable">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Cotización -->
    <div class="modal fade" id="quotationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="quotationForm">
                        <input type="hidden" id="orderId" name="order_id">
                        <div class="mb-3">
                            <label for="quotationNumber" class="form-label">Número de Cotización</label>
                            <input type="text" class="form-control" id="quotationNumber" name="quotation_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="validUntil" class="form-label">Válida hasta</label>
                            <input type="date" class="form-control" id="validUntil" name="valid_until" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <h6>Items de la Cotización</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="quotationItems">
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="totalAmount" class="form-label">Monto Total</label>
                                <input type="number" class="form-control" id="totalAmount" name="total_amount" step="0.01" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="submitQuotation()">Enviar Cotización</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let supplierData = null;
        let currentOrders = [];
        
        // Verificar autenticación
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            const userType = localStorage.getItem('userType');
            
            if(!token || userType !== 'supplier') {
                window.location.href = '/procurement/login.php';
                return;
            }
            
            supplierData = JSON.parse(localStorage.getItem('userData'));
            document.getElementById('companyName').textContent = supplierData.company_name;
            
            loadDashboard();
        });
        
        // Cargar dashboard
        async function loadDashboard() {
            try {
                const response = await fetch('api/supplier/dashboard_stats', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return loadDashboard();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    updateStats(result.data);
                    loadOrders();
                } else {
                    console.error('Error loading dashboard:', result.error);
                }
            } catch(error) {
                console.error('Error loading dashboard:', error);
            }
        }
        
        // Actualizar estadísticas
        function updateStats(data) {
            document.getElementById('totalOrders').textContent = data.orders.total_orders || 0;
            document.getElementById('pendingQuotations').textContent = data.pending_quotations || 0;
            document.getElementById('quotedOrders').textContent = data.orders.quoted_orders || 0;
            document.getElementById('approvedOrders').textContent = data.orders.approved_orders || 0;
        }
        
        // Función para renovar token
        async function refreshToken() {
            try {
                const response = await fetch('api/login_unified.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'username': 'proveedor@abc.com',
                        'password': 'proveedor123'
                    })
                });
                
                const result = await response.json();
                
                if(result.success) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('userData', JSON.stringify(result.user || result.supplier));
                    localStorage.setItem('userType', result.user ? 'admin' : 'supplier');
                    supplierData = result.user || result.supplier;
                    return true;
                } else {
                    console.error('Error refreshing token:', result.message);
                    window.location.href = '/procurement/views/login.php';
                    return false;
                }
            } catch(error) {
                console.error('Error refreshing token:', error);
                window.location.href = '/procurement/views/login.php';
                return false;
            }
        }

        // Cargar órdenes
        async function loadOrders() {
            try {
                const response = await fetch('api/supplier/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return loadOrders();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    currentOrders = result.data;
                    displayOrders(result.data);
                } else {
                    document.getElementById('ordersTable').innerHTML = 
                        '<tr><td colspan="7" class="text-center text-danger">Error al cargar órdenes</td></tr>';
                }
            } catch(error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersTable').innerHTML = 
                    '<tr><td colspan="7" class="text-center text-danger">Error de conexión</td></tr>';
            }
        }
        
        // Mostrar órdenes
        function displayOrders(orders) {
            const tbody = document.getElementById('ordersTable');
            
            if(orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay órdenes asignadas</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>${order.order_number}</td>
                    <td>${order.title}</td>
                    <td><span class="badge bg-${getStatusColor(order.assignment_status)}">${getStatusText(order.assignment_status)}</span></td>
                    <td><span class="badge bg-${getPriorityColor(order.priority)}">${order.priority || 'Normal'}</span></td>
                    <td>$${parseFloat(order.total_amount).toLocaleString()}</td>
                    <td>${order.required_date ? new Date(order.required_date).toLocaleDateString() : 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="viewOrder(${order.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${order.assignment_status === 'invited' ? 
                            `<button class="btn btn-sm btn-success" onclick="openQuotationModal(${order.id})">
                                <i class="fas fa-calculator"></i> Cotizar
                            </button>` : ''
                        }
                    </td>
                </tr>
            `).join('');
        }
        
        // Filtrar órdenes
        function filterOrders(filter) {
            let filteredOrders = currentOrders;
            
            if(filter === 'pending') {
                filteredOrders = currentOrders.filter(order => order.status === 'enviado');
            } else if(filter === 'quoted') {
                filteredOrders = currentOrders.filter(order => order.status === 'cotizado');
            }
            
            displayOrders(filteredOrders);
        }
        
        // Abrir modal de cotización
        async function openQuotationModal(orderId) {
            try {
                const response = await fetch(`api/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    const order = result.data;
                    document.getElementById('orderId').value = orderId;
                    document.getElementById('quotationNumber').value = 'COT-' + order.order_number;
                    
                    // Cargar items de la orden
                    loadOrderItems(orderId);
                    
                    // Mostrar modal
                    const modal = new bootstrap.Modal(document.getElementById('quotationModal'));
                    modal.show();
                }
            } catch(error) {
                console.error('Error loading order:', error);
                alert('Error al cargar la orden');
            }
        }
        
        // Cargar items de la orden
        async function loadOrderItems(orderId) {
            // Aquí cargarías los items de la orden desde la API
            // Por ahora, mostramos un ejemplo
            const itemsHtml = `
                <tr>
                    <td>Producto Ejemplo</td>
                    <td>10</td>
                    <td><input type="number" class="form-control form-control-sm" step="0.01" onchange="calculateTotal()"></td>
                    <td><span class="item-total">0.00</span></td>
                </tr>
            `;
            
            document.getElementById('quotationItems').innerHTML = itemsHtml;
            calculateTotal();
        }
        
        // Calcular total
        function calculateTotal() {
            const rows = document.querySelectorAll('#quotationItems tr');
            let total = 0;
            
            rows.forEach(row => {
                const quantity = parseFloat(row.cells[1].textContent) || 0;
                const price = parseFloat(row.cells[2].querySelector('input').value) || 0;
                const itemTotal = quantity * price;
                
                row.cells[3].querySelector('.item-total').textContent = itemTotal.toFixed(2);
                total += itemTotal;
            });
            
            document.getElementById('totalAmount').value = total.toFixed(2);
        }
        
        // Enviar cotización
        async function submitQuotation() {
            const formData = new FormData(document.getElementById('quotationForm'));
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api/orders/' + data.order_id + '/quotations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Cotización enviada exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('quotationModal')).hide();
                    loadOrders(); // Recargar órdenes
                } else {
                    alert('Error: ' + result.message);
                }
            } catch(error) {
                console.error('Error submitting quotation:', error);
                alert('Error al enviar cotización');
            }
        }
        
        // Funciones auxiliares
        function getStatusColor(status) {
            const colors = {
                'invited': 'warning',
                'responded': 'info',
                'selected': 'success',
                'rejected': 'danger',
                'borrador': 'secondary',
                'enviado': 'primary',
                'cotizado': 'info',
                'aprobado': 'success',
                'en_ejecucion': 'warning',
                'recibido': 'success',
                'cancelado': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        function getStatusText(status) {
            const texts = {
                'invited': 'Invitado',
                'responded': 'Respondido',
                'selected': 'Seleccionado',
                'rejected': 'Rechazado',
                'borrador': 'Borrador',
                'enviado': 'Enviado',
                'cotizado': 'Cotizado',
                'aprobado': 'Aprobado',
                'en_ejecucion': 'En Ejecución',
                'recibido': 'Recibido',
                'cancelado': 'Cancelado'
            };
            return texts[status] || status;
        }
        
        function getPriorityColor(priority) {
            const colors = {
                'low': 'success',
                'medium': 'warning',
                'high': 'danger',
                'urgent': 'dark'
            };
            return colors[priority] || 'secondary';
        }
        
        // Ver orden
        function viewOrder(orderId) {
            const order = currentOrders.find(o => o.id == orderId);
            if(!order) return;
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de la Orden</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Número:</strong></td><td>${order.order_number}</td></tr>
                            <tr><td><strong>Título:</strong></td><td>${order.title}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${order.description || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(order.assignment_status)}">${getStatusText(order.assignment_status)}</span></td></tr>
                            <tr><td><strong>Total:</strong></td><td><strong>$${parseFloat(order.total_amount).toLocaleString()}</strong></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Detalles Adicionales</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Fecha Creación:</strong></td><td>${new Date(order.created_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Fecha Requerida:</strong></td><td>${order.required_date ? new Date(order.required_date).toLocaleString() : 'N/A'}</td></tr>
                            <tr><td><strong>Invitado:</strong></td><td>${order.invited_at ? new Date(order.invited_at).toLocaleString() : 'N/A'}</td></tr>
                            <tr><td><strong>Respondido:</strong></td><td>${order.responded_at ? new Date(order.responded_at).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Items de la Orden</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Precio Estimado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.items && order.items.length > 0 ? 
                                        order.items.map(item => `
                                            <tr>
                                                <td>${item.product_name || 'N/A'}</td>
                                                <td>${item.description || 'N/A'}</td>
                                                <td>${item.quantity}</td>
                                                <td>${item.unit}</td>
                                                <td>$${parseFloat(item.estimated_price || 0).toLocaleString()}</td>
                                            </tr>
                                        `).join('') : 
                                        '<tr><td colspan="5" class="text-center text-muted">No hay items</td></tr>'
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            // Crear modal dinámico
            const modalHtml = `
                <div class="modal fade" id="viewOrderModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalles de la Orden</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${content}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                ${order.assignment_status === 'invited' ? 
                                    `<button type="button" class="btn btn-success" onclick="openQuotationModal(${order.id})">
                                        <i class="fas fa-calculator"></i> Enviar Cotización
                                    </button>` : ''
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal existente si existe
            const existingModal = document.getElementById('viewOrderModal');
            if(existingModal) {
                existingModal.remove();
            }
            
            // Agregar nuevo modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
            modal.show();
        }
        
        // Abrir modal de cotización
        function openQuotationModal(orderId) {
            const order = currentOrders.find(o => o.id == orderId);
            if(!order) return;
            
            document.getElementById('orderId').value = orderId;
            document.getElementById('quotationNumber').value = `COT-${order.order_number}-${Date.now()}`;
            
            // Establecer fecha de validez (30 días desde hoy)
            const validUntil = new Date();
            validUntil.setDate(validUntil.getDate() + 30);
            document.getElementById('validUntil').value = validUntil.toISOString().split('T')[0];
            
            // Cargar items de la orden
            loadOrderItems(order);
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('quotationModal'));
            modal.show();
        }
        
        // Cargar items de la orden para cotización
        function loadOrderItems(order) {
            const tbody = document.getElementById('quotationItems');
            
            if(!order.items || order.items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay items en esta orden</td></tr>';
                return;
            }
            
            tbody.innerHTML = order.items.map((item, index) => `
                <tr>
                    <td>
                        <input type="hidden" name="items[${index}][order_item_id]" value="${item.id}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${index}][unit]" value="${item.unit}">
                        ${item.product_name || 'Producto'}
                    </td>
                    <td>${item.quantity}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" 
                               name="items[${index}][unit_price]" 
                               step="0.01" min="0" 
                               onchange="calculateTotal()" 
                               placeholder="0.00">
                    </td>
                    <td>
                        <span class="item-total">$0.00</span>
                    </td>
                </tr>
            `).join('');
            
            calculateTotal();
        }
        
        // Calcular total de la cotización
        function calculateTotal() {
            let total = 0;
            const rows = document.querySelectorAll('#quotationItems tr');
            
            rows.forEach(row => {
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                const totalSpan = row.querySelector('.item-total');
                
                if(quantityInput && priceInput && totalSpan) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const itemTotal = quantity * price;
                    
                    totalSpan.textContent = `$${itemTotal.toLocaleString()}`;
                    total += itemTotal;
                }
            });
            
            document.getElementById('totalAmount').value = total.toFixed(2);
        }
        
        // Enviar cotización
        async function submitQuotation() {
            const form = document.getElementById('quotationForm');
            const formData = new FormData(form);
            
            // Convertir FormData a objeto
            const data = {
                order_id: formData.get('order_id'),
                quotation_number: formData.get('quotation_number'),
                valid_until: formData.get('valid_until'),
                notes: formData.get('notes'),
                items: []
            };
            
            // Recopilar items
            const rows = document.querySelectorAll('#quotationItems tr');
            rows.forEach(row => {
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                const orderItemIdInput = row.querySelector('input[name*="[order_item_id]"]');
                
                if(quantityInput && priceInput && orderItemIdInput) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const unitPrice = parseFloat(priceInput.value) || 0;
                    
                    if(quantity > 0 && unitPrice > 0) {
                        data.items.push({
                            order_item_id: orderItemIdInput.value,
                            quantity: quantity,
                            unit_price: unitPrice
                        });
                    }
                }
            });
            
            if(data.items.length === 0) {
                alert('Debe ingresar al menos un precio válido');
                return;
            }
            
            try {
                const response = await fetch('api/supplier/submit_quotation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(data)
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return submitQuotation();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Cotización enviada exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('quotationModal')).hide();
                    loadOrders(); // Recargar órdenes
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error submitting quotation:', error);
                alert('Error al enviar cotización');
            }
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
