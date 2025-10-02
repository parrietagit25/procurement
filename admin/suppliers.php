<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - Sistema de Procurement</title>
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
                        <a class="nav-link active" href="suppliers.php">
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
                        <h2><i class="fas fa-truck me-2"></i>Gestión de Proveedores</h2>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary me-3" onclick="showAddSupplierModal()">
                                <i class="fas fa-plus me-1"></i>Nuevo Proveedor
                            </button>
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
                                    <select class="form-select" id="statusFilter" onchange="filterSuppliers()">
                                        <option value="">Todos</option>
                                        <option value="pending">Pendiente</option>
                                        <option value="approved">Aprobado</option>
                                        <option value="suspended">Suspendido</option>
                                        <option value="rejected">Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchFilter" class="form-label">Buscar</label>
                                    <input type="text" class="form-control" id="searchFilter" placeholder="Empresa, contacto o email" onkeyup="filterSuppliers()">
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
                    
                    <!-- Tabla de Proveedores -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Contacto</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="suppliersTable">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando proveedores...
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

    <!-- Modal para Agregar/Editar Proveedor -->
    <div class="modal fade" id="supplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalTitle">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm">
                        <input type="hidden" id="supplierId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">Nombre de la Empresa *</label>
                                    <input type="text" class="form-control" id="companyName" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contactName" class="form-label">Nombre del Contacto *</label>
                                    <input type="text" class="form-control" id="contactName" name="contact_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="state" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="state" name="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postalCode" class="form-label">Código Postal</label>
                                    <input type="text" class="form-control" id="postalCode" name="postal_code">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taxId" class="form-label">RFC/Tax ID</label>
                                    <input type="text" class="form-control" id="taxId" name="tax_id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bankAccount" class="form-label">Cuenta Bancaria</label>
                                    <input type="text" class="form-control" id="bankAccount" name="bank_account">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSupplier()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Proveedor -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewSupplierContent">
                    <!-- Contenido se carga dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let userData = null;
        let currentSuppliers = [];
        
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
            
            loadSuppliers();
        });
        
        // Cargar proveedores
        async function loadSuppliers() {
            try {
                const response = await fetch('/api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    currentSuppliers = result.data;
                    displaySuppliers(result.data);
                } else {
                    document.getElementById('suppliersTable').innerHTML = 
                        '<tr><td colspan="7" class="text-center text-danger">Error al cargar proveedores</td></tr>';
                }
            } catch(error) {
                console.error('Error loading suppliers:', error);
                document.getElementById('suppliersTable').innerHTML = 
                    '<tr><td colspan="7" class="text-center text-danger">Error de conexión</td></tr>';
            }
        }
        
        // Mostrar proveedores
        function displaySuppliers(suppliers) {
            const tbody = document.getElementById('suppliersTable');
            
            if(suppliers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay proveedores</td></tr>';
                return;
            }
            
            tbody.innerHTML = suppliers.map(supplier => `
                <tr>
                    <td><strong>${supplier.company_name}</strong></td>
                    <td>${supplier.contact_name}</td>
                    <td>${supplier.email}</td>
                    <td>${supplier.phone || 'N/A'}</td>
                    <td><span class="badge status-badge bg-${getStatusColor(supplier.status)}">${getStatusText(supplier.status)}</span></td>
                    <td>${new Date(supplier.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewSupplier(${supplier.id})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editSupplier(${supplier.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${supplier.status === 'pending' ? 
                                `<button class="btn btn-sm btn-outline-success" onclick="approveSupplier(${supplier.id})" title="Aprobar">
                                    <i class="fas fa-check"></i>
                                </button>` : ''
                            }
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Filtrar proveedores
        function filterSuppliers() {
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            let filteredSuppliers = currentSuppliers;
            
            if(status) {
                filteredSuppliers = filteredSuppliers.filter(supplier => supplier.status === status);
            }
            
            if(search) {
                filteredSuppliers = filteredSuppliers.filter(supplier => 
                    supplier.company_name.toLowerCase().includes(search) || 
                    supplier.contact_name.toLowerCase().includes(search) ||
                    supplier.email.toLowerCase().includes(search)
                );
            }
            
            displaySuppliers(filteredSuppliers);
        }
        
        // Limpiar filtros
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('searchFilter').value = '';
            displaySuppliers(currentSuppliers);
        }
        
        // Funciones auxiliares
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'approved': 'success',
                'suspended': 'secondary',
                'rejected': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        function getStatusText(status) {
            const texts = {
                'pending': 'Pendiente',
                'approved': 'Aprobado',
                'suspended': 'Suspendido',
                'rejected': 'Rechazado'
            };
            return texts[status] || status;
        }
        
        // Acciones
        function showAddSupplierModal() {
            document.getElementById('supplierModalTitle').textContent = 'Nuevo Proveedor';
            document.getElementById('supplierForm').reset();
            document.getElementById('supplierId').value = '';
            new bootstrap.Modal(document.getElementById('supplierModal')).show();
        }
        
        async function viewSupplier(supplierId) {
            try {
                const response = await fetch(`/api/suppliers/${supplierId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showViewSupplierModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading supplier:', error);
                alert('Error al cargar el proveedor');
            }
        }
        
        async function editSupplier(supplierId) {
            try {
                const response = await fetch(`/api/suppliers/${supplierId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showEditSupplierModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading supplier:', error);
                alert('Error al cargar el proveedor');
            }
        }
        
        async function approveSupplier(supplierId) {
            if(confirm('¿Está seguro de aprobar este proveedor?')) {
                try {
                    const response = await fetch(`/api/suppliers/${supplierId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        }
                    });
                    
                    const result = await response.json();
                    
                    if(result.success) {
                        alert('Proveedor aprobado exitosamente');
                        loadSuppliers();
                    } else {
                        alert('Error: ' + (result.message || result.error));
                    }
                } catch(error) {
                    console.error('Error approving supplier:', error);
                    alert('Error al aprobar proveedor');
                }
            }
        }
        
        async function saveSupplier() {
            const formData = new FormData(document.getElementById('supplierForm'));
            const supplierId = document.getElementById('supplierId').value;
            
            try {
                const url = supplierId ? 
                    `/api/suppliers/${supplierId}` : 
                    '/api/suppliers';
                
                const method = supplierId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert(supplierId ? 'Proveedor actualizado exitosamente' : 'Proveedor creado exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
                    loadSuppliers();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error saving supplier:', error);
                alert('Error al guardar el proveedor');
            }
        }
        
        // Mostrar modal de vista de proveedor
        function showViewSupplierModal(supplierData) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Empresa:</strong></td><td>${supplierData.company_name}</td></tr>
                            <tr><td><strong>Contacto:</strong></td><td>${supplierData.contact_name}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${supplierData.email}</td></tr>
                            <tr><td><strong>Teléfono:</strong></td><td>${supplierData.phone || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(supplierData.status)}">${getStatusText(supplierData.status)}</span></td></tr>
                            <tr><td><strong>Fecha Registro:</strong></td><td>${new Date(supplierData.created_at).toLocaleString()}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información de Contacto</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Dirección:</strong></td><td>${supplierData.address || 'N/A'}</td></tr>
                            <tr><td><strong>Ciudad:</strong></td><td>${supplierData.city || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>${supplierData.state || 'N/A'}</td></tr>
                            <tr><td><strong>País:</strong></td><td>${supplierData.country || 'N/A'}</td></tr>
                            <tr><td><strong>Código Postal:</strong></td><td>${supplierData.postal_code || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Información Fiscal</h6>
                        <table class="table table-sm">
                            <tr><td><strong>RFC/Tax ID:</strong></td><td>${supplierData.tax_id || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Bancaria</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Cuenta Bancaria:</strong></td><td>${supplierData.bank_account || 'N/A'}</td></tr>
                            <tr><td><strong>Banco:</strong></td><td>${supplierData.bank_name || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('viewSupplierContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewSupplierModal')).show();
        }
        
        // Mostrar modal de edición de proveedor
        function showEditSupplierModal(supplierData) {
            document.getElementById('supplierModalTitle').textContent = 'Editar Proveedor';
            document.getElementById('supplierId').value = supplierData.id;
            document.getElementById('companyName').value = supplierData.company_name;
            document.getElementById('contactName').value = supplierData.contact_name;
            document.getElementById('email').value = supplierData.email;
            document.getElementById('phone').value = supplierData.phone || '';
            document.getElementById('address').value = supplierData.address || '';
            document.getElementById('city').value = supplierData.city || '';
            document.getElementById('state').value = supplierData.state || '';
            document.getElementById('postalCode').value = supplierData.postal_code || '';
            document.getElementById('taxId').value = supplierData.tax_id || '';
            document.getElementById('bankAccount').value = supplierData.bank_account || '';
            
            new bootstrap.Modal(document.getElementById('supplierModal')).show();
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
