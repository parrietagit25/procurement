<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Procurement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-shopping-cart me-2"></i>Sistema de Procurement</h3>
                        <p class="mb-0">Accede a tu cuenta</p>
                    </div>
                    <div class="login-body">
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario / Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                ¿Eres proveedor y no tienes cuenta? 
                                <a href="#" id="registerLink">Regístrate aquí</a>
                            </small>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="test.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-tools me-1"></i>Diagnóstico del Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Formulario de login
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('https://procurement.grupopcr.com.pa/api/login_unified.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    // Guardar token y datos del usuario
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('userData', JSON.stringify(result.user || result.supplier));
                    localStorage.setItem('userType', result.user ? 'admin' : 'supplier');
                    
                    // Redirigir según el tipo de usuario
                    if(result.user) {
                        window.location.href = 'https://procurement.grupopcr.com.pa/admin/dashboard.php';
                    } else {
                        window.location.href = 'https://procurement.grupopcr.com.pa/supplier/dashboard.php';
                    }
                } else {
                    alert('Error: ' + (result.message || result.error));
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión. Verifica que la API esté funcionando.');
            }
        });
        
        // Link de registro
        document.getElementById('registerLink').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Funcionalidad de registro en desarrollo');
        });
    </script>
</body>
</html>