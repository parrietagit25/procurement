<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Sistema de Procurement</title>
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
                        <a class="nav-link active" href="products.php">
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
                        <h2><i class="fas fa-box me-2"></i>Catálogo de Productos</h2>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary me-3" onclick="showAddProductModal()">
                                <i class="fas fa-plus me-1"></i>Nuevo Producto
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
                                    <label for="categoryFilter" class="form-label">Categoría</label>
                                    <select class="form-select" id="categoryFilter" onchange="filterProducts()">
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchFilter" class="form-label">Buscar</label>
                                    <input type="text" class="form-control" id="searchFilter" placeholder="Nombre o descripción" onkeyup="filterProducts()">
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
                    
                    <!-- Tabla de Productos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Unidad</th>
                                            <th>Precio Est.</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsTable">
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando productos...
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

    <!-- Modal para Agregar/Editar Producto -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId" name="id">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="productName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="productUnit" class="form-label">Unidad *</label>
                                    <select class="form-select" id="productUnit" name="unit" required>
                                        <option value="">Seleccionar</option>
                                        <option value="pieza">Pieza</option>
                                        <option value="kg">Kilogramo</option>
                                        <option value="litro">Litro</option>
                                        <option value="metro">Metro</option>
                                        <option value="m2">Metro cuadrado</option>
                                        <option value="m3">Metro cúbico</option>
                                        <option value="hora">Hora</option>
                                        <option value="día">Día</option>
                                        <option value="mes">Mes</option>
                                        <option value="año">Año</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productCategory" class="form-label">Categoría</label>
                                    <select class="form-select" id="productCategory" name="category_id">
                                        <option value="">Sin categoría</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productSupplier" class="form-label">Proveedor Principal</label>
                                    <select class="form-select" id="productSupplier" name="supplier_id">
                                        <option value="">Sin proveedor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Precio Estimado</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productPrice" name="estimated_price" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="productActive" name="is_active" checked>
                                        <label class="form-check-label" for="productActive">
                                            Activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Producto -->
    <div class="modal fade" id="viewProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewProductContent">
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
        let currentProducts = [];
        
        // Función para renovar token
        async function refreshToken() {
            try {
                const response = await fetch('api/login_unified.php', {
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
            
            loadProducts();
            loadCategories();
        });
        
        // Cargar productos
        async function loadProducts() {
            try {
                const response = await fetch('api/products', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    // Token expirado, renovar
                    await refreshToken();
                    return loadProducts(); // Reintentar
                }
                
                const result = await response.json();
                
                if(result.success) {
                    currentProducts = result.data;
                    displayProducts(result.data);
                } else {
                    document.getElementById('productsTable').innerHTML = 
                        '<tr><td colspan="6" class="text-center text-danger">Error al cargar productos</td></tr>';
                }
            } catch(error) {
                console.error('Error loading products:', error);
                document.getElementById('productsTable').innerHTML = 
                    '<tr><td colspan="6" class="text-center text-danger">Error de conexión</td></tr>';
            }
        }
        
        // Cargar categorías
        async function loadCategories() {
            try {
                const response = await fetch('api/categories', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    // Token expirado, renovar
                    await refreshToken();
                    return loadCategories(); // Reintentar
                }
                
                const result = await response.json();
                
                if(result.success) {
                    const categorySelect = document.getElementById('categoryFilter');
                    categorySelect.innerHTML = '<option value="">Todas</option>';
                    result.data.forEach(category => {
                        categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                    });
                }
            } catch(error) {
                console.error('Error loading categories:', error);
            }
        }
        
        // Mostrar productos
        function displayProducts(products) {
            const tbody = document.getElementById('productsTable');
            
            if(products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay productos</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => `
                <tr>
                    <td><strong>${product.name}</strong></td>
                    <td>${product.category_name || 'Sin categoría'}</td>
                    <td>${product.unit}</td>
                    <td>$${product.estimated_price || '0.00'}</td>
                    <td><span class="badge bg-${product.is_active ? 'success' : 'secondary'}">${product.is_active ? 'Activo' : 'Inactivo'}</span></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewProduct(${product.id})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editProduct(${product.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-${product.is_active ? 'warning' : 'success'}" onclick="toggleProductStatus(${product.id})" title="${product.is_active ? 'Desactivar' : 'Activar'}">
                                <i class="fas fa-${product.is_active ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id}, '${product.name}')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Filtrar productos
        function filterProducts() {
            const category = document.getElementById('categoryFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            let filteredProducts = currentProducts;
            
            if(category) {
                filteredProducts = filteredProducts.filter(product => product.category_id == category);
            }
            
            if(search) {
                filteredProducts = filteredProducts.filter(product => 
                    product.name.toLowerCase().includes(search) || 
                    (product.description && product.description.toLowerCase().includes(search))
                );
            }
            
            displayProducts(filteredProducts);
        }
        
        // Limpiar filtros
        function clearFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('searchFilter').value = '';
            displayProducts(currentProducts);
        }
        
        // Acciones
        function showAddProductModal() {
            document.getElementById('productModalTitle').textContent = 'Nuevo Producto';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            loadSuppliersForSelect();
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }
        
        async function viewProduct(productId) {
            try {
                const response = await fetch(`api/products/${productId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showViewProductModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading product:', error);
                alert('Error al cargar el producto');
            }
        }
        
        async function editProduct(productId) {
            try {
                const response = await fetch(`api/products/${productId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showEditProductModal(result.data);
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error loading product:', error);
                alert('Error al cargar el producto');
            }
        }
        
        // Cargar proveedores para el select
        async function loadSuppliersForSelect() {
            try {
                const response = await fetch('api/suppliers', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                if(response.status === 401) {
                    // Token expirado, renovar
                    await refreshToken();
                    return loadSuppliersForSelect(); // Reintentar
                }
                
                const result = await response.json();
                
                if(result.success) {
                    const supplierSelect = document.getElementById('productSupplier');
                    supplierSelect.innerHTML = '<option value="">Sin proveedor</option>';
                    result.data.forEach(supplier => {
                        if(supplier.status === 'approved') {
                            supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.company_name}</option>`;
                        }
                    });
                }
            } catch(error) {
                console.error('Error loading suppliers:', error);
            }
        }
        
        // Mostrar modal de vista de producto
        function showViewProductModal(productData) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nombre:</strong></td><td>${productData.name}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${productData.description || 'Sin descripción'}</td></tr>
                            <tr><td><strong>Unidad:</strong></td><td>${productData.unit}</td></tr>
                            <tr><td><strong>Precio Estimado:</strong></td><td>$${productData.estimated_price || '0.00'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${productData.is_active ? 'success' : 'secondary'}">${productData.is_active ? 'Activo' : 'Inactivo'}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Clasificación</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Categoría:</strong></td><td>${productData.category_name || 'Sin categoría'}</td></tr>
                            <tr><td><strong>Proveedor Principal:</strong></td><td>${productData.supplier_name || 'Sin proveedor'}</td></tr>
                            <tr><td><strong>Fecha Creación:</strong></td><td>${new Date(productData.created_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Última Actualización:</strong></td><td>${new Date(productData.updated_at).toLocaleString()}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('viewProductContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewProductModal')).show();
        }
        
        // Mostrar modal de edición de producto
        async function showEditProductModal(productData) {
            document.getElementById('productModalTitle').textContent = 'Editar Producto';
            document.getElementById('productId').value = productData.id;
            document.getElementById('productName').value = productData.name;
            document.getElementById('productDescription').value = productData.description || '';
            document.getElementById('productUnit').value = productData.unit;
            document.getElementById('productPrice').value = productData.estimated_price || '';
            document.getElementById('productActive').checked = productData.is_active == 1;
            
            // Cargar categorías y proveedores
            await loadCategoriesForSelect();
            await loadSuppliersForSelect();
            
            // Seleccionar valores actuales
            document.getElementById('productCategory').value = productData.category_id || '';
            document.getElementById('productSupplier').value = productData.supplier_id || '';
            
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }
        
        // Cargar categorías para el select
        async function loadCategoriesForSelect() {
            try {
                const response = await fetch('api/categories', {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                
                const result = await response.json();
                
                if(result.success) {
                    const categorySelect = document.getElementById('productCategory');
                    categorySelect.innerHTML = '<option value="">Sin categoría</option>';
                    result.data.forEach(category => {
                        categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                    });
                }
            } catch(error) {
                console.error('Error loading categories:', error);
            }
        }
        
        // Guardar producto
        async function saveProduct() {
            const formData = new FormData(document.getElementById('productForm'));
            const productId = document.getElementById('productId').value;
            
            // Convertir FormData a objeto y manejar tipos correctamente
            const data = Object.fromEntries(formData);
            
            // Convertir checkbox a boolean
            data.is_active = document.getElementById('productActive').checked;
            
            // Convertir category_id a número si existe
            if(data.category_id) {
                data.category_id = parseInt(data.category_id);
            }
            
            // Convertir estimated_price a número si existe
            if(data.estimated_price) {
                data.estimated_price = parseFloat(data.estimated_price);
            }
            
            try {
                const url = productId ? 
                    `api/products/${productId}` : 
                    'api/products';
                
                const method = productId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(data)
                });
                
                if(response.status === 401) {
                    // Token expirado, renovar
                    await refreshToken();
                    // Reintentar la operación
                    const retryResponse = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        },
                        body: JSON.stringify(data)
                    });
                    const retryResult = await retryResponse.json();
                    
                    if(retryResult.success) {
                        alert(productId ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
                        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                        loadProducts();
                    } else {
                        alert('Error: ' + (retryResult.message || retryResult.error));
                    }
                    return;
                }
                
                const result = await response.json();
                
                if(result.success) {
                    alert(productId ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    loadProducts();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error saving product:', error);
                alert('Error al guardar el producto');
            }
        }
        
        // Eliminar producto
        async function deleteProduct(productId, productName) {
            if(confirm(`¿Está seguro de que desea eliminar el producto "${productName}"?\n\nEsta acción no se puede deshacer.`)) {
                try {
                    const response = await fetch(`api/products/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        }
                    });
                    
                    if(response.status === 401) {
                        await refreshToken();
                        return deleteProduct(productId, productName);
                    }
                    
                    const result = await response.json();
                    
                    if(result.success) {
                        alert(`Producto "${result.deleted_product}" eliminado exitosamente`);
                        loadProducts();
                    } else {
                        if(result.used_in_orders) {
                            alert(`No se puede eliminar el producto porque está siendo usado en ${result.used_in_orders} órdenes de compra`);
                        } else {
                            alert('Error: ' + (result.message || result.error));
                        }
                    }
                } catch(error) {
                    console.error('Error deleting product:', error);
                    alert('Error al eliminar el producto');
                }
            }
        }
        
        // Cambiar estado del producto (activar/desactivar)
        async function toggleProductStatus(productId) {
            try {
                const response = await fetch(`api/products/${productId}/toggle-status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify({})
                });
                
                if(response.status === 401) {
                    await refreshToken();
                    return toggleProductStatus(productId);
                }
                
                const result = await response.json();
                
                if(result.success) {
                    alert(`Producto "${result.product_name}" ${result.message}`);
                    loadProducts();
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error toggling product status:', error);
                alert('Error al cambiar el estado del producto');
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
