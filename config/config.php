<?php
// Configuración general del sistema
define('SITE_NAME', 'Sistema de Procurement');
define('SITE_URL', 'http://localhost/procurement');
define('ADMIN_EMAIL', 'admin@procurement.com');

// Configuración de JWT
define('JWT_SECRET', 'be56844ad6445bea26cf8679eb9fc9e8ec0fa54b341f69994783920ecfbc4796');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600); // 1 hora

// Configuración de archivos
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Estados de órdenes
define('ORDER_STATUS_DRAFT', 'borrador');
define('ORDER_STATUS_SENT', 'enviado');
define('ORDER_STATUS_QUOTED', 'cotizado');
define('ORDER_STATUS_APPROVED', 'aprobado');
define('ORDER_STATUS_IN_PROGRESS', 'en_ejecucion');
define('ORDER_STATUS_RECEIVED', 'recibido');
define('ORDER_STATUS_CANCELLED', 'cancelado');

// Tipos de usuario
define('USER_TYPE_ADMIN', 'admin');
define('USER_TYPE_SUPPLIER', 'supplier');

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu_email@gmail.com');
define('SMTP_PASSWORD', 'tu_password_app');
define('FROM_EMAIL', 'noreply@procurement.com');
define('FROM_NAME', 'Sistema de Procurement');

// Configuración de paginación
define('ITEMS_PER_PAGE', 20);

// Timezone
date_default_timezone_set('America/Mexico_City');
?>