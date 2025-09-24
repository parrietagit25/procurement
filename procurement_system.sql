-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2025 a las 22:02:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `procurement_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `approvals`
--

CREATE TABLE `approvals` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `entity_type` enum('order','quotation','invoice','delivery') NOT NULL,
  `entity_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `is_active`, `created_at`) VALUES
(1, 'Materiales de Oficina', 'Papelería, útiles de escritorio, etc.', NULL, 1, '2025-09-22 20:21:10'),
(2, 'Tecnología', 'Equipos de cómputo, software, hardware', NULL, 1, '2025-09-22 20:21:10'),
(3, 'Servicios', 'Consultoría, mantenimiento, limpieza', NULL, 1, '2025-09-22 20:21:10'),
(4, 'Mobiliario', 'Muebles de oficina y equipamiento', NULL, 1, '2025-09-22 20:21:10'),
(5, 'Suministros', 'Materiales de construcción, herramientas', NULL, 1, '2025-09-22 20:21:10'),
(6, 'Tecnología', 'Productos y servicios tecnológicos', NULL, 1, '2025-09-23 16:22:21'),
(7, 'Oficina', 'Suministros de oficina', NULL, 1, '2025-09-23 16:22:21'),
(8, 'Mantenimiento', 'Productos de mantenimiento', NULL, 1, '2025-09-23 16:22:21'),
(9, 'Servicios', 'Servicios profesionales', NULL, 1, '2025-09-23 16:22:21'),
(10, 'Tecnología', 'Productos y servicios tecnológicos', NULL, 1, '2025-09-23 16:25:12'),
(11, 'Oficina', 'Suministros de oficina', NULL, 1, '2025-09-23 16:25:12'),
(12, 'Mantenimiento', 'Productos de mantenimiento', NULL, 1, '2025-09-23 16:25:12'),
(13, 'Servicios', 'Servicios profesionales', NULL, 1, '2025-09-23 16:25:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `delivery_date` date NOT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','in_transit','delivered','received') DEFAULT 'scheduled',
  `received_by` int(11) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'MXN',
  `status` enum('pending','approved','paid','rejected') DEFAULT 'pending',
  `file_path` varchar(500) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_suppliers`
--

CREATE TABLE `order_suppliers` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `status` enum('invited','responded','selected','rejected') DEFAULT 'invited',
  `invited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_suppliers`
--

INSERT INTO `order_suppliers` (`id`, `order_id`, `supplier_id`, `status`, `invited_at`, `responded_at`) VALUES
(1, 3, 1, 'invited', '2025-09-23 15:50:16', NULL),
(2, 3, 2, 'invited', '2025-09-23 15:50:16', NULL),
(4, 1, 2, 'invited', '2025-09-23 15:54:26', NULL),
(5, 4, 1, 'responded', '2025-09-23 17:08:08', '2025-09-23 18:10:29'),
(6, 4, 2, 'invited', '2025-09-23 17:08:08', NULL),
(7, 4, 3, 'invited', '2025-09-23 17:08:08', NULL),
(8, 4, 4, 'invited', '2025-09-23 17:08:08', NULL),
(11, 10, 4, 'invited', '2025-09-23 19:07:12', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category_id`, `unit`, `estimated_price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Dell Inspiron 15', 'Laptop para desarrollo de software', 1, 'pieza', 15000.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(2, 'Mouse Logitech MX Master', 'Mouse inalámbrico profesional', 1, 'pieza', 1200.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(3, 'Teclado Mecánico Corsair', 'Teclado para programación', 1, 'pieza', 2500.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(4, 'Monitor Samsung 24\"', 'Monitor Full HD para oficina', 1, 'pieza', 3500.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(5, 'Papel Bond A4', 'Papel para impresión', 2, 'resma', 45.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(6, 'ashhh', 'ashhh', 12, 'pieza', 25.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:38:51'),
(7, 'Carpetas Manila', 'Carpetas para archivo', 2, 'pieza', 15.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(8, 'Servicio de Limpieza', 'Limpieza de oficinas', 4, 'hora', 150.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(9, 'Mantenimiento de Equipos', 'Mantenimiento preventivo', 3, 'hora', 200.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(10, 'Internet Empresarial', 'Conexión de internet', 4, 'mes', 800.00, 1, '2025-09-23 16:25:12', '2025-09-23 16:25:12'),
(12, 'Producto Test', 'Descripción test', 1, 'pieza', 100.00, 1, '2025-09-23 16:30:39', '2025-09-23 16:30:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `requested_by` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('borrador','enviado','cotizado','aprobado','en_ejecucion','recibido','cancelado') DEFAULT 'borrador',
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'MXN',
  `required_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `order_number`, `title`, `description`, `requested_by`, `department`, `priority`, `status`, `total_amount`, `currency`, `required_date`, `created_at`, `updated_at`) VALUES
(1, 'PO2025090001', 'prueba de orden ', 'prueba de orden ', 1, 'prueba de orden ', 'medium', 'enviado', 100.00, 'MXN', '2025-09-23', '2025-09-23 15:32:40', '2025-09-23 15:54:26'),
(3, 'PO2025090002', 'Orden de Prueba 2', 'Esta es una orden de prueba para testing', 1, 'IT', 'medium', 'borrador', 1500.00, 'MXN', NULL, '2025-09-23 15:50:16', '2025-09-23 15:50:16'),
(4, 'PO2025090003', 'orden de prueba', 'descripcion de prueba para la orden ', 1, 'IT', 'urgent', 'enviado', 200.00, 'MXN', '2025-09-23', '2025-09-23 17:07:18', '2025-09-23 17:08:08'),
(5, 'ORD-2025-004', 'Equipos de Oficina', 'Compra de equipos de computación para la oficina', 1, 'IT', 'high', 'enviado', 2500.00, 'MXN', '2025-02-15', '2025-09-23 18:19:04', '2025-09-23 18:19:04'),
(6, 'ORD-2025-005', 'Material de Limpieza', 'Productos de limpieza para el mantenimiento', 1, 'Mantenimiento', 'medium', 'cotizado', 800.00, 'MXN', '2025-02-20', '2025-09-23 18:19:04', '2025-09-23 18:19:04'),
(7, 'ORD-2025-006', 'Servicios de Consultoría', 'Servicios de consultoría en sistemas', 1, 'IT', 'urgent', 'aprobado', 5000.00, 'MXN', '2025-02-10', '2025-09-23 18:19:04', '2025-09-23 18:19:04'),
(8, 'ORD-2025-007', 'Mobiliario', 'Sillas y mesas para la oficina', 1, 'RRHH', 'medium', 'en_ejecucion', 1200.00, 'MXN', '2025-02-25', '2025-09-23 18:19:04', '2025-09-23 18:19:04'),
(9, 'ORD-2025-008', 'Software Licencias', 'Licencias de software para el año 2025', 1, 'IT', 'high', 'recibido', 3000.00, 'MXN', '2025-01-30', '2025-09-23 18:19:04', '2025-09-23 18:19:04'),
(10, 'PO2025090004', 'Pureba de Orden', 'prueba de descripcion', 1, 'Prueba de departamento', 'medium', 'enviado', 18500.00, 'MXN', '2025-09-23', '2025-09-23 18:33:27', '2025-09-23 18:35:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `order_id`, `product_id`, `product_name`, `description`, `quantity`, `unit`, `estimated_price`, `total_price`, `created_at`) VALUES
(1, 1, NULL, 'prueba de orden ', 'prueba de orden ', 10.00, 'pieza', 10.00, 100.00, '2025-09-23 15:32:40'),
(2, 3, NULL, 'Laptop Dell', 'Laptop para desarrollo', 2.00, 'pieza', 500.00, 1000.00, '2025-09-23 15:50:16'),
(3, 3, NULL, 'Mouse Logitech', 'Mouse inalámbrico', 5.00, 'pieza', 25.00, 125.00, '2025-09-23 15:50:16'),
(4, 3, NULL, 'Teclado Mecánico', 'Teclado para programación', 2.00, 'pieza', 150.00, 300.00, '2025-09-23 15:50:16'),
(5, 3, NULL, 'Monitor 24\"', 'Monitor Full HD', 1.00, 'pieza', 200.00, 200.00, '2025-09-23 15:50:16'),
(6, 4, NULL, 'prueba de producto', 'se necesita varios servicios ', 20.00, 'servicio', 10.00, 200.00, '2025-09-23 17:07:18'),
(7, 5, NULL, 'Producto A', 'Descripción del producto A', 2.00, 'pcs', 1000.00, NULL, '2025-09-23 18:19:04'),
(8, 5, NULL, 'Producto B', 'Descripción del producto B', 1.00, 'pcs', 1500.00, NULL, '2025-09-23 18:19:04'),
(9, 6, NULL, 'Producto A', 'Descripción del producto A', 2.00, 'pcs', 320.00, NULL, '2025-09-23 18:19:04'),
(10, 6, NULL, 'Producto B', 'Descripción del producto B', 1.00, 'pcs', 480.00, NULL, '2025-09-23 18:19:04'),
(11, 7, NULL, 'Producto A', 'Descripción del producto A', 2.00, 'pcs', 2000.00, NULL, '2025-09-23 18:19:04'),
(12, 7, NULL, 'Producto B', 'Descripción del producto B', 1.00, 'pcs', 3000.00, NULL, '2025-09-23 18:19:04'),
(13, 8, NULL, 'Producto A', 'Descripción del producto A', 2.00, 'pcs', 480.00, NULL, '2025-09-23 18:19:04'),
(14, 8, NULL, 'Producto B', 'Descripción del producto B', 1.00, 'pcs', 720.00, NULL, '2025-09-23 18:19:04'),
(15, 9, NULL, 'Producto A', 'Descripción del producto A', 2.00, 'pcs', 1200.00, NULL, '2025-09-23 18:19:04'),
(16, 9, NULL, 'Producto B', 'Descripción del producto B', 1.00, 'pcs', 1800.00, NULL, '2025-09-23 18:19:04'),
(17, 10, NULL, 'prueba de producto 1', 'descripcion de prueba de producto 1', 10.00, 'pieza', 200.00, 2000.00, '2025-09-23 18:33:27'),
(18, 10, NULL, 'prueba de producto 2', 'descripcion prueba de producto 2', 55.00, 'litro', 300.00, 16500.00, '2025-09-23 18:33:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `quotation_number` varchar(50) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'MXN',
  `valid_until` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','expired') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `quotations`
--

INSERT INTO `quotations` (`id`, `order_id`, `supplier_id`, `quotation_number`, `total_amount`, `currency`, `valid_until`, `notes`, `status`, `submitted_at`, `reviewed_at`, `reviewed_by`) VALUES
(1, 1, 1, NULL, 15000.00, 'MXN', '2025-10-23', 'Cotización de prueba con descuento por volumen', 'pending', '2025-09-23 16:53:09', NULL, NULL),
(2, 1, 1, NULL, 15000.00, 'MXN', '2025-10-23', 'Cotización de prueba con descuento por volumen', 'pending', '2025-09-23 16:54:06', NULL, NULL),
(3, 4, 1, 'COT-PO2025090003-1758650829116', 300.00, 'MXN', '2025-10-25', 'marca chain', 'pending', '2025-09-23 18:10:29', NULL, NULL),
(4, 10, 6, 'COT-PO2025090004-1758653915705', 3370.00, 'MXN', '2025-10-23', 'estos son los precios finales', 'pending', '2025-09-23 18:59:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `order_item_id`, `unit_price`, `total_price`, `notes`) VALUES
(1, 2, 1, 2500.00, 5000.00, 'Precio especial por volumen'),
(2, 3, 6, 15.00, 300.00, NULL),
(3, 4, 17, 7.00, 70.00, NULL),
(4, 4, 18, 60.00, 3300.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'México',
  `postal_code` varchar(20) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','suspended','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_name`, `contact_name`, `email`, `password_hash`, `phone`, `address`, `city`, `state`, `country`, `postal_code`, `tax_id`, `bank_account`, `bank_name`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 'Proveedor ABC S.A. de C.V.', 'Roberto Silva', 'proveedor1@abc.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '555-0101', 'Av. Principal 123, Col. Centro', 'Ciudad de México', 'CDMX', 'México', NULL, NULL, NULL, NULL, 'approved', NULL, NULL, '2025-09-22 20:37:41', '2025-09-23 17:59:50'),
(2, 'Suministros XYZ S.A.', 'Patricia Morales', 'proveedor2@xyz.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '555-0202', 'Calle Secundaria 456, Zona Industrial', 'Guadalajara', 'Jalisco', 'México', NULL, NULL, NULL, NULL, 'approved', NULL, NULL, '2025-09-22 20:37:41', '2025-09-23 18:49:03'),
(3, 'Servicios Técnicos LMN', 'Miguel Torres', 'proveedor3@lmn.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '555-0303', 'Blvd. Norte 789, Fracc. Comercial', 'Monterrey', 'Nuevo León', 'México', NULL, NULL, NULL, NULL, 'approved', NULL, '2025-09-23 16:11:55', '2025-09-22 20:37:41', '2025-09-23 18:49:06'),
(4, 'Materiales de Construcción RST', 'Carmen Herrera', 'proveedor4@rst.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '555-0404', 'Carretera Sur Km 15', 'Puebla', 'Puebla', 'México', NULL, NULL, NULL, NULL, 'approved', NULL, NULL, '2025-09-22 20:37:41', '2025-09-23 18:49:10'),
(5, 'Proveedor de Prueba S.A.', 'Juan Pérez', 'juan@proveedorprueba.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '555-1234', 'Calle Test 123', 'Ciudad de México', 'CDMX', 'México', '12345', 'RFC123456789', '1234567890', 'Banco Test', 'pending', NULL, NULL, '2025-09-23 16:05:52', '2025-09-23 18:49:12'),
(6, 'prueba provee', 'prueba provee', 'casco@casco.com', '$2y$10$/dg.2mi3bU5Psumgcaf/7OVh1tQRBj3izECd/A0Bjl8LGVXmTYEdW', '60026773', 'prueba provee', 'Panama', 'panama', 'México', '2002', '2151212', '512051202020', NULL, 'approved', NULL, '2025-09-23 18:41:08', '2025-09-23 16:14:05', '2025-09-23 18:49:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'company_name', 'Mi Empresa S.A. de C.V.', 'Nombre de la empresa', '2025-09-22 20:21:10'),
(2, 'company_address', 'Calle Principal 123, Ciudad, Estado', 'Dirección de la empresa', '2025-09-22 20:21:10'),
(3, 'approval_limit_level_1', '10000', 'Límite de aprobación nivel 1 (pesos)', '2025-09-22 20:21:10'),
(4, 'approval_limit_level_2', '50000', 'Límite de aprobación nivel 2 (pesos)', '2025-09-22 20:21:10'),
(5, 'quotation_days', '7', 'Días para responder cotizaciones', '2025-09-22 20:21:10'),
(6, 'email_notifications', '1', 'Activar notificaciones por email', '2025-09-22 20:21:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','buyer','approver','viewer') DEFAULT 'buyer',
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `department`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@procurement.com', '$2y$10$5rRn1TVuKYGblwpYGWs0z.VeFtAJKJ5Xms/.WKkUyDnBCyNmtlrAi', 'Administrador', 'Sistema', 'admin', NULL, 1, '2025-09-22 20:21:10', '2025-09-22 20:21:10'),
(2, 'compras1', 'compras1@empresa.com', '$2y$10$PHOahEKhIBcFGhNv/C/8yu3NNm/hScuoZP1zKHFNkC4MbVxsdCJu2', 'María', 'González', 'buyer', 'Compras', 1, '2025-09-22 20:35:58', '2025-09-22 20:35:58'),
(3, 'compras2', 'compras2@empresa.com', '$2y$10$KY8KoETUhV9jk.Q0676WDe/2.oW2ZdbVozavQV1qWGlaj8pll2dSC', 'Carlos', 'López', 'buyer', 'Compras', 1, '2025-09-22 20:35:59', '2025-09-22 20:35:59'),
(4, 'aprobador', 'aprobador@empresa.com', '$2y$10$2ECYXeeNPhbpW9EhHUsIoe57kGz3WtWKeuD0rA4.Iypr0W8PiynQq', 'Ana', 'Martínez', 'approver', 'Gerencia', 1, '2025-09-22 20:35:59', '2025-09-22 20:35:59'),
(5, 'viewer', 'viewer@empresa.com', '$2y$10$kCBoG4tYZLl2FzNxBR.myOdgcd44penAQcOedNkvBU2Nxg4f2EO16', 'Luis', 'Rodríguez', 'viewer', 'Finanzas', 1, '2025-09-22 20:35:59', '2025-09-22 20:35:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indices de la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indices de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `received_by` (`received_by`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indices de la tabla `order_suppliers`
--
ALTER TABLE `order_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indices de la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indices de la tabla `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indices de la tabla `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `order_suppliers`
--
ALTER TABLE `order_suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `deliveries_ibfk_3` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Filtros para la tabla `order_suppliers`
--
ALTER TABLE `order_suppliers`
  ADD CONSTRAINT `order_suppliers_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_suppliers_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotations_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `quotations_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotation_items_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `purchase_order_items` (`id`);

--
-- Filtros para la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
