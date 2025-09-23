<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Procurement</title>
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
                        <a class="nav-link" href="quotations.php">
                            <i class="fas fa-calculator me-2"></i>Cotizaciones
                        </a>
                        <a class="nav-link active" href="reports.php">
                            <i class="fas fa-chart-bar me-2"></i>Reportes
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Bienvenido, <strong id="userName">Usuario</strong></span>
                            <button class="btn btn-outline-danger btn-sm" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </button>
                        </div>
                    </div>
                    
                    <!-- Información -->
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h4>Sistema de Reportes</h4>
                            <p class="text-muted">Esta funcionalidad está en desarrollo. Podrás generar reportes detallados del sistema.</p>
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                                            <h6>Reportes de Órdenes</h6>
                                            <small class="text-muted">Análisis de compras por período</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-truck fa-2x text-success mb-2"></i>
                                            <h6>Reportes de Proveedores</h6>
                                            <small class="text-muted">Rendimiento y evaluación</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>
                                            <h6>Reportes Financieros</h6>
                                            <small class="text-muted">Análisis de costos y ahorros</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                                            <h6>Dashboards</h6>
                                            <small class="text-muted">Visualizaciones interactivas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        });
        
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
