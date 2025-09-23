-- Sistema de Procurement - Esquema de Base de Datos
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS procurement_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE procurement_system;

-- Tabla de usuarios (administradores internos)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'buyer', 'approver', 'viewer') DEFAULT 'buyer',
    department VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de proveedores
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(200) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'México',
    postal_code VARCHAR(20),
    tax_id VARCHAR(50),
    bank_account VARCHAR(50),
    bank_name VARCHAR(100),
    status ENUM('pending', 'approved', 'suspended', 'rejected') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Tabla de categorías de productos/servicios
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

-- Tabla de productos/servicios
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    unit VARCHAR(50) NOT NULL, -- pieza, kg, litro, hora, etc.
    estimated_price DECIMAL(10,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Tabla de órdenes de compra
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    requested_by INT NOT NULL,
    department VARCHAR(100),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('borrador', 'enviado', 'cotizado', 'aprobado', 'en_ejecucion', 'recibido', 'cancelado') DEFAULT 'borrador',
    total_amount DECIMAL(12,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'MXN',
    required_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requested_by) REFERENCES users(id)
);

-- Tabla de items de órdenes de compra
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    description TEXT,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    estimated_price DECIMAL(10,2),
    total_price DECIMAL(12,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Tabla de proveedores asignados a órdenes
CREATE TABLE order_suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    supplier_id INT NOT NULL,
    status ENUM('invited', 'responded', 'selected', 'rejected') DEFAULT 'invited',
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Tabla de cotizaciones
CREATE TABLE quotations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    supplier_id INT NOT NULL,
    quotation_number VARCHAR(50),
    total_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'MXN',
    valid_until DATE,
    notes TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- Tabla de items de cotizaciones
CREATE TABLE quotation_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    order_item_id INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES purchase_order_items(id)
);

-- Tabla de aprobaciones
CREATE TABLE approvals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    approver_id INT NOT NULL,
    level INT NOT NULL, -- nivel de aprobación (1, 2, 3...)
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comments TEXT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(id)
);

-- Tabla de entregas
CREATE TABLE deliveries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    supplier_id INT NOT NULL,
    delivery_date DATE NOT NULL,
    tracking_number VARCHAR(100),
    notes TEXT,
    status ENUM('scheduled', 'in_transit', 'delivered', 'received') DEFAULT 'scheduled',
    received_by INT NULL,
    received_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (received_by) REFERENCES users(id)
);

-- Tabla de facturas
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    supplier_id INT NOT NULL,
    invoice_number VARCHAR(100) NOT NULL,
    invoice_date DATE NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'MXN',
    status ENUM('pending', 'approved', 'paid', 'rejected') DEFAULT 'pending',
    file_path VARCHAR(500),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    processed_by INT NULL,
    FOREIGN KEY (order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- Tabla de notificaciones
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL, -- NULL para notificaciones globales
    supplier_id INT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Tabla de archivos adjuntos
CREATE TABLE attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM('order', 'quotation', 'invoice', 'delivery') NOT NULL,
    entity_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Tabla de configuración del sistema
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar datos iniciales
INSERT INTO users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@procurement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'admin');

INSERT INTO categories (name, description) VALUES
('Materiales de Oficina', 'Papelería, útiles de escritorio, etc.'),
('Tecnología', 'Equipos de cómputo, software, hardware'),
('Servicios', 'Consultoría, mantenimiento, limpieza'),
('Mobiliario', 'Muebles de oficina y equipamiento'),
('Suministros', 'Materiales de construcción, herramientas');

INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('company_name', 'Mi Empresa S.A. de C.V.', 'Nombre de la empresa'),
('company_address', 'Calle Principal 123, Ciudad, Estado', 'Dirección de la empresa'),
('approval_limit_level_1', '10000', 'Límite de aprobación nivel 1 (pesos)'),
('approval_limit_level_2', '50000', 'Límite de aprobación nivel 2 (pesos)'),
('quotation_days', '7', 'Días para responder cotizaciones'),
('email_notifications', '1', 'Activar notificaciones por email');
