<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones - Sistema de Procurement</title>
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
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-file-invoice me-2"></i>Órdenes
                        </a>
                        <a class="nav-link" href="suppliers.php">
                            <i class="fas fa-truck me-2"></i>Proveedores
                        </a>
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box me-2"></i>Productos
                        </a>
                        <a class="nav-link active" href="quotations.php">
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
                        <h2><i class="fas fa-calculator me-2"></i>Gestión de Cotizaciones</h2>
                        <div class="d-flex align-items-center">
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
                                    <label for="orderFilter" class="form-label">Orden</label>
                                    <select class="form-select" id="orderFilter" onchange="filterQuotations()">
                                        <option value="">Todas las órdenes</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="supplierFilter" class="form-label">Proveedor</label>
                                    <select class="form-select" id="supplierFilter" onchange="filterQuotations()">
                                        <option value="">Todos los proveedores</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Estado</label>
                                    <select class="form-select" id="statusFilter" onchange="filterQuotations()">
                                        <option value="">Todos</option>
                                        <option value="pending">Pendiente</option>
                                        <option value="accepted">Aceptada</option>
                                        <option value="rejected">Rechazada</option>
                                        <option value="expired">Expirada</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="searchFilter" class="form-label">Buscar</label>
                                    <input type="text" class="form-control" id="searchFilter" placeholder="Proveedor o orden" onkeyup="filterQuotations()">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times me-1"></i>Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Cotizaciones -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Orden</th>
                                            <th>Proveedor</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Válida Hasta</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quotationsTable">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando cotizaciones...
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

    <!-- Modal para Ver Cotización -->
    <div class="modal fade" id="viewQuotationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewQuotationContent">
                    <!-- Contenido se carga dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Evaluar Cotización -->
    <div class="modal fade" id="evaluateQuotationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evaluar Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="evaluateQuotationForm">
                        <input type="hidden" id="evaluateQuotationId" name="id">
                        <div class="mb-3">
                            <label for="evaluateStatus" class="form-label">Decisión *</label>
                            <select class="form-select" id="evaluateStatus" name="status" required>
                                <option value="accepted">Aceptada</option>
                                <option value="rejected">Rechazada</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="evaluationNotes" class="form-label">Notas de Revisión</label>
                            <textarea class="form-control" id="evaluationNotes" name="notes" rows="4" placeholder="Comentarios sobre la revisión..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveEvaluation()">Guardar Evaluación</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let userData = null;
        
        // Función para renovar token
        async function refreshToken() {
            try {
                const response = await fetch('https://procurement.grupopcr.com.pa/api/login_unified.php', {
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
            
            loadQuotations();
            loadOrders();
            loadSuppliers();
        });
        
        // Variables globales
        let currentQuotations = [];
        
        // Cargar cotizaciones
        async function loadQuotations() {
            try {
                const response = await fetch('https://procurement.grupopcr.com.pa/api/quotations', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return loadQuotations();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    currentQuotations = result.data;
                    displayQuotations(result.data);
                } else {
                    document.getElementById('quotationsTable').innerHTML = 
                        '<tr><td colspan="7" class="text-center text-danger">Error al cargar cotizaciones</td></tr>';
                }
            } catch(error) {
                console.error('Error loading quotations:', error);
                document.getElementById('quotationsTable').innerHTML = 
                    '<tr><td colspan="7" class="text-center text-danger">Error de conexión</td></tr>';
            }
        }
        
        // Cargar órdenes para filtro
        async function loadOrders() {
            try {
                const response = await fetch('https://procurement.grupopcr.com.pa/api/orders', {
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
                    const orderSelect = document.getElementById('orderFilter');
                    orderSelect.innerHTML = '<option value="">Todas las órdenes</option>';
                    result.data.forEach(order => {
                        orderSelect.innerHTML += `<option value="${order.id}">${order.order_number} - ${order.title}</option>`;
                    });
                }
            } catch(error) {
                console.error('Error loading orders:', error);
            }
        }
        
        // Cargar proveedores para filtro
        async function loadSuppliers() {
            try {
                const response = await fetch('https://procurement.grupopcr.com.pa/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return loadSuppliers();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    const supplierSelect = document.getElementById('supplierFilter');
                    supplierSelect.innerHTML = '<option value="">Todos los proveedores</option>';
                    result.data.forEach(supplier => {
                        supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.company_name}</option>`;
                    });
                }
            } catch(error) {
                console.error('Error loading suppliers:', error);
            }
        }
        
        // Mostrar cotizaciones
        function displayQuotations(quotations) {
            const tbody = document.getElementById('quotationsTable');
            
            if(quotations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay cotizaciones</td></tr>';
                return;
            }
            
            tbody.innerHTML = quotations.map(quotation => `
                <tr>
                    <td>
                        <strong>${quotation.order_number}</strong><br>
                        <small class="text-muted">${quotation.order_title}</small>
                    </td>
                    <td>
                        <strong>${quotation.supplier_name}</strong><br>
                        <small class="text-muted">${quotation.contact_name}</small>
                    </td>
                    <td><strong>$${parseFloat(quotation.total_amount).toLocaleString()}</strong></td>
                    <td><span class="badge bg-${getStatusColor(quotation.status)}">${getStatusText(quotation.status)}</span></td>
                    <td>${new Date(quotation.submitted_at).toLocaleDateString()}</td>
                    <td>${quotation.valid_until ? new Date(quotation.valid_until).toLocaleDateString() : 'N/A'}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewQuotation(${quotation.id})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${quotation.status === 'pending' ? 
                                `<button class="btn btn-sm btn-outline-success" onclick="evaluateQuotation(${quotation.id})" title="Evaluar">
                                    <i class="fas fa-check"></i>
                                </button>` : ''
                            }
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Filtrar cotizaciones
        function filterQuotations() {
            const order = document.getElementById('orderFilter').value;
            const supplier = document.getElementById('supplierFilter').value;
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            let filteredQuotations = currentQuotations;
            
            if(order) {
                filteredQuotations = filteredQuotations.filter(q => q.order_id == order);
            }
            
            if(supplier) {
                filteredQuotations = filteredQuotations.filter(q => q.supplier_id == supplier);
            }
            
            if(status) {
                filteredQuotations = filteredQuotations.filter(q => q.status === status);
            }
            
            if(search) {
                filteredQuotations = filteredQuotations.filter(q => 
                    q.supplier_name.toLowerCase().includes(search) || 
                    q.order_title.toLowerCase().includes(search)
                );
            }
            
            displayQuotations(filteredQuotations);
        }
        
        // Limpiar filtros
        function clearFilters() {
            document.getElementById('orderFilter').value = '';
            document.getElementById('supplierFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchFilter').value = '';
            displayQuotations(currentQuotations);
        }
        
        // Ver cotización
        async function viewQuotation(quotationId) {
            try {
                const response = await fetch(`https://procurement.grupopcr.com.pa/api/quotations/${quotationId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return viewQuotation(quotationId);
                }
                
                const result = await response.json();
                
                if(result.success) {
                    showViewQuotationModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading quotation:', error);
                alert('Error al cargar la cotización');
            }
        }
        
        // Evaluar cotización
        function evaluateQuotation(quotationId) {
            document.getElementById('evaluateQuotationId').value = quotationId;
            new bootstrap.Modal(document.getElementById('evaluateQuotationModal')).show();
        }
        
        // Mostrar modal de vista de cotización
        function showViewQuotationModal(quotationData) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de la Cotización</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Orden:</strong></td><td>${quotationData.order_number} - ${quotationData.order_title}</td></tr>
                            <tr><td><strong>Proveedor:</strong></td><td>${quotationData.supplier_name}</td></tr>
                            <tr><td><strong>Contacto:</strong></td><td>${quotationData.contact_name} - ${quotationData.email}</td></tr>
                            <tr><td><strong>Teléfono:</strong></td><td>${quotationData.phone || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(quotationData.status)}">${getStatusText(quotationData.status)}</span></td></tr>
                            <tr><td><strong>Total:</strong></td><td><strong>$${parseFloat(quotationData.total_amount).toLocaleString()}</strong></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Detalles Adicionales</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Fecha Envío:</strong></td><td>${new Date(quotationData.submitted_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Válida Hasta:</strong></td><td>${quotationData.valid_until ? new Date(quotationData.valid_until).toLocaleString() : 'N/A'}</td></tr>
                            <tr><td><strong>Revisada por:</strong></td><td>${quotationData.first_name ? `${quotationData.first_name} ${quotationData.last_name}` : 'N/A'}</td></tr>
                            <tr><td><strong>Notas:</strong></td><td>${quotationData.notes || 'Sin notas'}</td></tr>
                            <tr><td><strong>Fecha Revisión:</strong></td><td>${quotationData.reviewed_at ? new Date(quotationData.reviewed_at).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Items de la Cotización</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto/Servicio</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${quotationData.items && quotationData.items.length > 0 ? 
                                        quotationData.items.map(item => `
                                            <tr>
                                                <td>${item.product_name}</td>
                                                <td>${item.description || 'N/A'}</td>
                                                <td>${item.quantity}</td>
                                                <td>${item.unit}</td>
                                                <td>$${parseFloat(item.unit_price).toLocaleString()}</td>
                                                <td>$${parseFloat(item.total_price).toLocaleString()}</td>
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
            
            document.getElementById('viewQuotationContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewQuotationModal')).show();
        }
        
        // Guardar evaluación
        async function saveEvaluation() {
            const formData = new FormData(document.getElementById('evaluateQuotationForm'));
            const quotationId = document.getElementById('evaluateQuotationId').value;
            
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(`https://procurement.grupopcr.com.pa/api/quotations/${quotationId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(data)
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return saveEvaluation();
                }
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Evaluación guardada exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('evaluateQuotationModal')).hide();
                    loadQuotations();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error saving evaluation:', error);
                alert('Error al guardar la evaluación');
            }
        }
        
        // Funciones auxiliares
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'accepted': 'success',
                'rejected': 'danger',
                'expired': 'secondary'
            };
            return colors[status] || 'secondary';
        }
        
        function getStatusText(status) {
            const texts = {
                'pending': 'Pendiente',
                'accepted': 'Aceptada',
                'rejected': 'Rechazada',
                'expired': 'Expirada'
            };
            return texts[status] || status;
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
