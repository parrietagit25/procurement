<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes de Compra - Sistema de Procurement</title>
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
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        .priority-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
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
                        <a class="nav-link" href="create_order.php">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Orden
                        </a>
                        <a class="nav-link active" href="orders.php">
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
                        <h2><i class="fas fa-file-invoice me-2"></i>Órdenes de Compra</h2>
                        <div class="d-flex align-items-center">
                            <a href="create_order.php" class="btn btn-primary me-3">
                                <i class="fas fa-plus me-1"></i>Nueva Orden
                            </a>
                            <span class="me-3">Bienvenido, <strong id="userName">Usuario</strong></span>
                            <button class="btn btn-outline-danger btn-sm" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Estado</label>
                                    <select class="form-select" id="statusFilter" onchange="filterOrders()">
                                        <option value="">Todos</option>
                                        <option value="borrador">Borrador</option>
                                        <option value="enviado">Enviado</option>
                                        <option value="cotizado">Cotizado</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="en_ejecucion">En Ejecución</option>
                                        <option value="recibido">Recibido</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="priorityFilter" class="form-label">Prioridad</label>
                                    <select class="form-select" id="priorityFilter" onchange="filterOrders()">
                                        <option value="">Todas</option>
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchFilter" class="form-label">Buscar</label>
                                    <input type="text" class="form-control" id="searchFilter" placeholder="Título o número de orden" onkeyup="filterOrders()">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Órdenes -->
                    <div class="card">
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
                                            <th>Solicitado por</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ordersTable">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando órdenes...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Orden -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewOrderContent">
                    <!-- Contenido se carga dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Orden -->
    <div class="modal fade" id="editOrderModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Orden de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editOrderForm">
                        <input type="hidden" id="editOrderId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTitle" class="form-label">Título de la Orden *</label>
                                    <input type="text" class="form-control" id="editTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPriority" class="form-label">Prioridad *</label>
                                    <select class="form-select" id="editPriority" name="priority" required>
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editDepartment" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="editDepartment" name="department">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editRequiredDate" class="form-label">Fecha Requerida</label>
                                    <input type="date" class="form-control" id="editRequiredDate" name="required_date">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Estado</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="borrador">Borrador</option>
                                <option value="enviado">Enviado</option>
                                <option value="cotizado">Cotizado</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="en_ejecucion">En Ejecución</option>
                                <option value="recibido">Recibido</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveOrderEdit()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gestionar Proveedores -->
    <div class="modal fade" id="manageSuppliersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestionar Proveedores de la Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="manageOrderId" name="order_id">
                    <div class="mb-3">
                        <h6>Proveedores Asignados</h6>
                        <div id="assignedSuppliers">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Agregar Nuevos Proveedores</h6>
                        <div id="availableSuppliers">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSupplierAssignments()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let userData = null;
        let currentOrders = [];
        
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
            
            loadOrders();
        });
        
        // Cargar órdenes
        async function loadOrders() {
            try {
                const response = await fetch('/procurement/api/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    currentOrders = result.data;
                    displayOrders(result.data);
                } else {
                    document.getElementById('ordersTable').innerHTML = 
                        '<tr><td colspan="8" class="text-center text-danger">Error al cargar órdenes</td></tr>';
                }
            } catch(error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersTable').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>';
            }
        }
        
        // Mostrar órdenes
        function displayOrders(orders) {
            const tbody = document.getElementById('ordersTable');
            
            if(orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay órdenes</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td><strong>${order.order_number}</strong></td>
                    <td>${order.title}</td>
                    <td><span class="badge status-badge bg-${getStatusColor(order.status)}">${getStatusText(order.status)}</span></td>
                    <td><span class="badge priority-badge bg-${getPriorityColor(order.priority)}">${getPriorityText(order.priority)}</span></td>
                    <td>$${order.total_amount.toLocaleString()}</td>
                    <td>${order.first_name} ${order.last_name}</td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.id})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editOrder(${order.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="manageSuppliers(${order.id})" title="Proveedores">
                                <i class="fas fa-truck"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Filtrar órdenes
        function filterOrders() {
            const status = document.getElementById('statusFilter').value;
            const priority = document.getElementById('priorityFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            let filteredOrders = currentOrders;
            
            if(status) {
                filteredOrders = filteredOrders.filter(order => order.status === status);
            }
            
            if(priority) {
                filteredOrders = filteredOrders.filter(order => order.priority === priority);
            }
            
            if(search) {
                filteredOrders = filteredOrders.filter(order => 
                    order.title.toLowerCase().includes(search) || 
                    order.order_number.toLowerCase().includes(search)
                );
            }
            
            displayOrders(filteredOrders);
        }
        
        // Limpiar filtros
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('priorityFilter').value = '';
            document.getElementById('searchFilter').value = '';
            displayOrders(currentOrders);
        }
        
        // Funciones auxiliares
        function getStatusColor(status) {
            const colors = {
                'borrador': 'secondary',
                'enviado': 'warning',
                'cotizado': 'info',
                'aprobado': 'success',
                'en_ejecucion': 'primary',
                'recibido': 'dark',
                'cancelado': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        function getStatusText(status) {
            const texts = {
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
        
        function getPriorityText(priority) {
            const texts = {
                'low': 'Baja',
                'medium': 'Media',
                'high': 'Alta',
                'urgent': 'Urgente'
            };
            return texts[priority] || priority;
        }
        
        // Acciones
        async function viewOrder(orderId) {
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showViewOrderModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading order:', error);
                alert('Error al cargar la orden');
            }
        }
        
        async function editOrder(orderId) {
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showEditOrderModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading order:', error);
                alert('Error al cargar la orden');
            }
        }
        
        async function manageSuppliers(orderId) {
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showManageSuppliersModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading order:', error);
                alert('Error al cargar la orden');
            }
        }
        
        // Mostrar modal de vista de orden
        function showViewOrderModal(orderData) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Número:</strong></td><td>${orderData.order_number}</td></tr>
                            <tr><td><strong>Título:</strong></td><td>${orderData.title}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(orderData.status)}">${getStatusText(orderData.status)}</span></td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${getPriorityColor(orderData.priority)}">${getPriorityText(orderData.priority)}</span></td></tr>
                            <tr><td><strong>Departamento:</strong></td><td>${orderData.department || 'N/A'}</td></tr>
                            <tr><td><strong>Fecha Requerida:</strong></td><td>${orderData.required_date || 'N/A'}</td></tr>
                            <tr><td><strong>Monto Total:</strong></td><td>$${orderData.total_amount.toLocaleString()}</td></tr>
                            <tr><td><strong>Solicitado por:</strong></td><td>${orderData.first_name} ${orderData.last_name}</td></tr>
                            <tr><td><strong>Fecha Creación:</strong></td><td>${new Date(orderData.created_at).toLocaleString()}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Descripción</h6>
                        <p>${orderData.description || 'Sin descripción'}</p>
                        
                        <h6>Proveedores Asignados</h6>
                        <div class="list-group">
                            ${orderData.suppliers && orderData.suppliers.length > 0 ? 
                                orderData.suppliers.map(supplier => `
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${supplier.company_name}</strong><br>
                                            <small>${supplier.contact_name} - ${supplier.email}</small>
                                        </div>
                                        <span class="badge bg-${supplier.assignment_status === 'invited' ? 'warning' : 'success'}">${supplier.assignment_status}</span>
                                    </div>
                                `).join('') : 
                                '<p class="text-muted">No hay proveedores asignados</p>'
                            }
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Items de la Orden</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto/Servicio</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Precio Est.</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${orderData.items && orderData.items.length > 0 ? 
                                        orderData.items.map(item => `
                                            <tr>
                                                <td>${item.product_name}</td>
                                                <td>${item.description || 'N/A'}</td>
                                                <td>${item.quantity}</td>
                                                <td>${item.unit}</td>
                                                <td>$${item.estimated_price || '0.00'}</td>
                                                <td>$${item.total_price || '0.00'}</td>
                                            </tr>
                                        `).join('') : 
                                        '<tr><td colspan="6" class="text-center text-muted">No hay items</td></tr>'
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('viewOrderContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewOrderModal')).show();
        }
        
        // Mostrar modal de edición de orden
        function showEditOrderModal(orderData) {
            document.getElementById('editOrderId').value = orderData.id;
            document.getElementById('editTitle').value = orderData.title;
            document.getElementById('editPriority').value = orderData.priority;
            document.getElementById('editDepartment').value = orderData.department || '';
            document.getElementById('editRequiredDate').value = orderData.required_date || '';
            document.getElementById('editDescription').value = orderData.description || '';
            document.getElementById('editStatus').value = orderData.status;
            
            new bootstrap.Modal(document.getElementById('editOrderModal')).show();
        }
        
        // Guardar edición de orden
        async function saveOrderEdit() {
            const formData = new FormData(document.getElementById('editOrderForm'));
            const orderId = document.getElementById('editOrderId').value;
            
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Orden actualizada exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
                    loadOrders();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error updating order:', error);
                alert('Error al actualizar la orden');
            }
        }
        
        // Mostrar modal de gestión de proveedores
        async function showManageSuppliersModal(orderData) {
            document.getElementById('manageOrderId').value = orderData.id;
            
            // Mostrar proveedores asignados
            const assignedHtml = orderData.suppliers && orderData.suppliers.length > 0 ? 
                orderData.suppliers.map(supplier => `
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${supplier.company_name}</strong><br>
                                <small>${supplier.contact_name} - ${supplier.email}</small>
                            </div>
                            <div>
                                <span class="badge bg-${supplier.assignment_status === 'invited' ? 'warning' : 'success'} me-2">${supplier.assignment_status}</span>
                                <button class="btn btn-sm btn-outline-danger" onclick="removeSupplier(${supplier.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('') : 
                '<p class="text-muted">No hay proveedores asignados</p>';
            
            document.getElementById('assignedSuppliers').innerHTML = assignedHtml;
            
            // Cargar proveedores disponibles
            try {
                const response = await fetch('/procurement/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    const availableHtml = result.data.map(supplier => `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="${supplier.id}" id="supplier_${supplier.id}">
                            <label class="form-check-label" for="supplier_${supplier.id}">
                                <strong>${supplier.company_name}</strong> - ${supplier.contact_name}
                                <span class="badge bg-${supplier.status === 'approved' ? 'success' : 'warning'} ms-2">${supplier.status}</span>
                            </label>
                        </div>
                    `).join('');
                    
                    document.getElementById('availableSuppliers').innerHTML = availableHtml;
                }
            } catch(error) {
                console.error('Error loading suppliers:', error);
                document.getElementById('availableSuppliers').innerHTML = '<p class="text-danger">Error al cargar proveedores</p>';
            }
            
            new bootstrap.Modal(document.getElementById('manageSuppliersModal')).show();
        }
        
        // Guardar asignaciones de proveedores
        async function saveSupplierAssignments() {
            const orderId = document.getElementById('manageOrderId').value;
            const checkboxes = document.querySelectorAll('#availableSuppliers input[type="checkbox"]:checked');
            const supplierIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}/suppliers`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify({supplier_ids: supplierIds})
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Proveedores asignados exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('manageSuppliersModal')).hide();
                    loadOrders();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error assigning suppliers:', error);
                alert('Error al asignar proveedores');
            }
        }
        
        // Remover proveedor de la orden
        async function removeSupplier(supplierId) {
            const orderId = document.getElementById('manageOrderId').value;
            
            if(!confirm('¿Está seguro de que desea remover este proveedor de la orden?')) {
                return;
            }
            
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}/suppliers/${supplierId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return removeSupplier(supplierId);
                }
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Proveedor removido exitosamente');
                    // Recargar la lista de proveedores asignados
                    manageSuppliers(orderId);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error removing supplier:', error);
                alert('Error al remover proveedor');
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
