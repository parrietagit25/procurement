<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Procurement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
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
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="create_order.php">
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
                        <h2>Dashboard</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Bienvenido, <strong id="userName">Usuario</strong></span>
                            <button class="btn btn-outline-danger btn-sm" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </button>
                        </div>
                    </div>
                    
                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <!-- Estadísticas -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-number" id="totalOrders">0</div>
                                        <div class="stat-label">Total Órdenes</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-number" id="pendingOrders">0</div>
                                        <div class="stat-label">Pendientes</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-number" id="approvedSuppliers">0</div>
                                        <div class="stat-label">Proveedores Aprobados</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-number" id="totalAmount">$0</div>
                                        <div class="stat-label">Monto Total</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráficos -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-chart-line me-2"></i>Órdenes por Mes</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="ordersChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-chart-pie me-2"></i>Estado de Órdenes</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="statusChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Órdenes Recientes -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-clock me-2"></i>Órdenes Recientes</h5>
                                <button class="btn btn-primary btn-sm" onclick="viewAllOrders()">
                                    Ver Todas
                                </button>
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
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentOrders">
                                            <tr>
                                                <td colspan="7" class="text-center">Cargando...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other sections will be loaded here -->
                    <div id="otherContent" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        let userData = null;
        let ordersChart = null;
        let statusChart = null;
        
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
            
            loadDashboard();
        });
        
        // Función para renovar token
        async function refreshToken() {
            try {
                const response = await fetch('/procurement/api/login_unified.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'username': 'admin',
                        'password': 'admin123'
                    })
                });
                
                const result = await response.json();
                
                if(result.success) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('userData', JSON.stringify(result.user || result.supplier));
                    localStorage.setItem('userType', result.user ? 'admin' : 'supplier');
                    userData = result.user || result.supplier;
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

        // Cargar dashboard
        async function loadDashboard() {
            try {
                const response = await fetch('/api/admin/dashboard_stats', {
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
                    loadRecentOrders();
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
            document.getElementById('pendingOrders').textContent = data.orders.draft_orders + data.orders.sent_orders || 0;
            document.getElementById('approvedSuppliers').textContent = data.suppliers.approved || 0;
            document.getElementById('totalAmount').textContent = '$' + (data.orders.total_amount || 0).toLocaleString();
            
            // Crear gráficos
            createCharts(data);
        }
        
        // Crear gráficos
        function createCharts(data) {
            // Gráfico de órdenes por mes
            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
            if(ordersChart) ordersChart.destroy();
            
            ordersChart = new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: data.orders_by_month.map(item => item.month),
                    datasets: [{
                        label: 'Órdenes',
                        data: data.orders_by_month.map(item => item.count),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // Gráfico de estado de órdenes
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            if(statusChart) statusChart.destroy();
            
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Borrador', 'Enviado', 'Cotizado', 'Aprobado', 'En Ejecución', 'Recibido'],
                    datasets: [{
                        data: [
                            data.orders.draft_orders || 0,
                            data.orders.sent_orders || 0,
                            data.orders.quoted_orders || 0,
                            data.orders.approved_orders || 0,
                            data.orders.in_progress_orders || 0,
                            data.orders.received_orders || 0
                        ],
                        backgroundColor: [
                            '#6c757d',
                            '#ffc107',
                            '#17a2b8',
                            '#28a745',
                            '#007bff',
                            '#6f42c1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Cargar órdenes recientes
        async function loadRecentOrders() {
            try {
                const response = await fetch('api/orders?limit=5', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return loadRecentOrders();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    displayRecentOrders(result.data);
                } else {
                    console.error('Error loading recent orders:', result.error);
                }
            } catch(error) {
                console.error('Error loading recent orders:', error);
            }
        }
        
        // Mostrar órdenes recientes
        function displayRecentOrders(orders) {
            const tbody = document.getElementById('recentOrders');
            
            if(orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay órdenes recientes</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>${order.order_number}</td>
                    <td>${order.title}</td>
                    <td><span class="badge bg-${getStatusColor(order.status)}">${order.status}</span></td>
                    <td><span class="badge bg-${getPriorityColor(order.priority)}">${order.priority}</span></td>
                    <td>$${order.total_amount.toLocaleString()}</td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
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
        
        function getPriorityColor(priority) {
            const colors = {
                'low': 'success',
                'medium': 'warning',
                'high': 'danger',
                'urgent': 'dark'
            };
            return colors[priority] || 'secondary';
        }
        
        
        // Cerrar sesión
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('userData');
            localStorage.removeItem('userType');
            window.location.href = '/procurement/views/login.php';
        }
        
        // Ver orden
        async function viewOrder(orderId) {
            try {
                const response = await fetch(`/procurement/api/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return viewOrder(orderId);
                }
                
                const result = await response.json();
                
                if(result.success) {
                    showOrderModal(result.data);
                } else {
                    alert('Error al cargar la orden: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading order:', error);
                alert('Error al cargar la orden');
            }
        }
        
        // Mostrar modal de orden
        function showOrderModal(order) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Número:</strong></td><td>${order.order_number}</td></tr>
                            <tr><td><strong>Título:</strong></td><td>${order.title}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${order.description || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(order.status)}">${order.status}</span></td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${getPriorityColor(order.priority)}">${order.priority}</span></td></tr>
                            <tr><td><strong>Departamento:</strong></td><td>${order.department || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Detalles Financieros</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Monto Total:</strong></td><td><strong>$${parseFloat(order.total_amount).toLocaleString()}</strong></td></tr>
                            <tr><td><strong>Moneda:</strong></td><td>${order.currency || 'USD'}</td></tr>
                            <tr><td><strong>Fecha Requerida:</strong></td><td>${order.required_date ? new Date(order.required_date).toLocaleDateString() : 'N/A'}</td></tr>
                            <tr><td><strong>Fecha Creación:</strong></td><td>${new Date(order.created_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Última Actualización:</strong></td><td>${new Date(order.updated_at).toLocaleString()}</td></tr>
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
                                        <th>Subtotal</th>
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
                                                <td>$${parseFloat((item.quantity * item.estimated_price) || 0).toLocaleString()}</td>
                                            </tr>
                                        `).join('') : 
                                        '<tr><td colspan="6" class="text-center text-muted">No hay items</td></tr>'
                                    }
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="5"><strong>Total:</strong></td>
                                        <td><strong>$${parseFloat(order.total_amount).toLocaleString()}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                ${order.suppliers && order.suppliers.length > 0 ? `
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Proveedores Asignados</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Contacto</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.suppliers.map(supplier => `
                                        <tr>
                                            <td>${supplier.company_name}</td>
                                            <td>${supplier.contact_name}</td>
                                            <td>${supplier.email}</td>
                                            <td>${supplier.phone || 'N/A'}</td>
                                            <td><span class="badge bg-${getSupplierStatusColor(supplier.status)}">${supplier.status}</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
            
            // Crear modal dinámico
            const modalHtml = `
                <div class="modal fade" id="viewOrderModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalles de la Orden - ${order.order_number}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${content}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <a href="orders.php" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver en Órdenes
                                </a>
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
        
        // Función auxiliar para colores de estado de proveedores
        function getSupplierStatusColor(status) {
            const colors = {
                'invited': 'warning',
                'responded': 'info',
                'selected': 'success',
                'rejected': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        // Ver todas las órdenes
        function viewAllOrders() {
            window.location.href = 'orders.php';
        }
    </script>
</body>
</html>
