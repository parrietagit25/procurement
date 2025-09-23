# Sistema de Procurement

Sistema completo de gestión de compras con portal para proveedores externos, desarrollado en PHP, MySQL, JavaScript, CSS y HTML.

## 🚀 Características Principales

### Portal Interno (Administradores)
- Dashboard con estadísticas y gráficos
- Gestión de órdenes de compra
- Administración de proveedores
- Sistema de cotizaciones
- Flujo de aprobaciones
- Reportes y análisis

### Portal Externo (Proveedores)
- Dashboard personalizado
- Visualización de órdenes asignadas
- Sistema de cotizaciones en línea
- Gestión de entregas
- Seguimiento de facturas
- Notificaciones automáticas

### Funcionalidades Técnicas
- Autenticación JWT segura
- API REST completa
- Sistema de notificaciones por email
- Interfaz responsive (mobile-first)
- Base de datos MySQL optimizada
- Arquitectura modular y escalable

## 📋 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL, JSON, Mail

## 🛠️ Instalación

### 1. Clonar el Proyecto
```bash
git clone [url-del-repositorio]
cd procurement
```

### 2. Configurar Base de Datos
1. Crear una base de datos MySQL llamada `procurement_system`
2. Importar el esquema:
```bash
mysql -u root -p procurement_system < database/schema.sql
```

### 3. Configurar Variables de Entorno
Editar `config/config.php`:
```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'procurement_system');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// Configuración de JWT
define('JWT_SECRET', 'tu_clave_secreta_muy_segura');

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'tu_email@gmail.com');
define('SMTP_PASSWORD', 'tu_password_app');
```

### 4. Configurar Permisos
```bash
chmod 755 uploads/
chmod 644 config/*.php
```

### 5. Configurar Servidor Web
#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🚀 Uso del Sistema

### Acceso al Sistema
1. **Portal Interno**: `http://localhost/procurement/`
2. **Portal Proveedores**: `http://localhost/procurement/supplier/`

### Credenciales por Defecto
- **Administrador**: 
  - Usuario: `admin`
  - Contraseña: `password`
- **Proveedor de Prueba**:
  - Email: `proveedor@ejemplo.com`
  - Contraseña: `supplier123`

## 📁 Estructura del Proyecto

```
procurement/
├── admin/                  # Portal interno
│   └── dashboard.php
├── api/                    # API REST
│   ├── index.php
│   └── endpoints/
├── classes/                # Clases PHP
│   ├── User.php
│   ├── Supplier.php
│   ├── PurchaseOrder.php
│   ├── Auth.php
│   ├── JWT.php
│   └── Notification.php
├── config/                 # Configuración
│   ├── database.php
│   └── config.php
├── database/               # Esquemas SQL
│   └── schema.sql
├── supplier/               # Portal proveedores
│   └── dashboard.php
├── uploads/                # Archivos subidos
├── views/                  # Vistas compartidas
│   └── login.php
├── index.php              # Punto de entrada
└── README.md
```

## 🔧 API Endpoints

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión

### Usuarios
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Obtener usuario
- `PUT /api/users/{id}` - Actualizar usuario

### Proveedores
- `GET /api/suppliers` - Listar proveedores
- `POST /api/suppliers` - Crear proveedor
- `GET /api/suppliers/{id}` - Obtener proveedor
- `POST /api/suppliers/{id}/approve` - Aprobar proveedor

### Órdenes de Compra
- `GET /api/orders` - Listar órdenes
- `POST /api/orders` - Crear orden
- `GET /api/orders/{id}` - Obtener orden
- `PUT /api/orders/{id}` - Actualizar orden
- `PUT /api/orders/{id}/status` - Actualizar estado

### Dashboard
- `GET /api/dashboard/stats` - Estadísticas

## 🎨 Personalización

### Temas y Colores
Los colores principales se pueden modificar en los archivos CSS:
- Portal Interno: Gradiente azul-púrpura
- Portal Proveedores: Gradiente verde

### Configuración de Notificaciones
Editar `classes/Notification.php` para personalizar:
- Templates de email
- Tipos de notificaciones
- Configuración SMTP

## 🔒 Seguridad

- Autenticación JWT con expiración
- Sanitización de datos de entrada
- Validación de permisos por endpoint
- Protección contra inyección SQL (PDO)
- Headers de seguridad CORS

## 📊 Flujo de Trabajo

1. **Creación de Orden**: Administrador crea orden de compra
2. **Asignación de Proveedores**: Se asignan proveedores a la orden
3. **Notificación**: Los proveedores reciben notificación por email
4. **Cotización**: Proveedores envían cotizaciones a través del portal
5. **Evaluación**: Administradores evalúan y aprueban cotizaciones
6. **Seguimiento**: Seguimiento de entregas y facturas

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
- Verificar credenciales en `config/database.php`
- Asegurar que MySQL esté ejecutándose
- Verificar que la base de datos existe

### Error de Autenticación JWT
- Verificar que `JWT_SECRET` esté configurado
- Limpiar localStorage del navegador
- Verificar que el token no haya expirado

### Problemas de Email
- Verificar configuración SMTP
- Usar credenciales de aplicación para Gmail
- Verificar que el servidor tenga habilitado el envío de emails

## 📈 Próximas Mejoras

- [ ] Integración con ERP
- [ ] Notificaciones push
- [ ] App móvil
- [ ] Firma electrónica
- [ ] Reportes avanzados
- [ ] Integración con WhatsApp Business
- [ ] Sistema de auditoría
- [ ] Backup automático

## 📞 Soporte

Para soporte técnico o consultas:
- Email: soporte@procurement.com
- Documentación: [Enlace a documentación]
- Issues: [Enlace a GitHub Issues]

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

---

**Desarrollado con ❤️ para optimizar procesos de compras**
