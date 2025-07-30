-- Sistema de Reservaciones Database Schema
-- MySQL 5.7 Compatible

CREATE DATABASE IF NOT EXISTS reservaciones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reservaciones;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('admin', 'comercio', 'cliente') NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de amenidades
CREATE TABLE amenidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    capacidad_maxima INT DEFAULT 1,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de reservaciones
CREATE TABLE reservaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_reservacion DATE NOT NULL,
    hora_reservacion TIME NOT NULL,
    numero_personas INT NOT NULL,
    amenidad_id INT,
    estado ENUM('pendiente', 'confirmado', 'rechazado', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10,2) DEFAULT 0.00,
    personal_asignado_id INT,
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (amenidad_id) REFERENCES amenidades(id),
    FOREIGN KEY (personal_asignado_id) REFERENCES usuarios(id)
);

-- Tabla de compras
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_entrega ENUM('pickup', 'domicilio') NOT NULL,
    direccion_entrega TEXT,
    fecha_entrega DATE,
    hora_entrega TIME,
    estado ENUM('pendiente', 'confirmado', 'rechazado', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10,2) DEFAULT 0.00,
    personal_asignado_id INT,
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (personal_asignado_id) REFERENCES usuarios(id)
);

-- Tabla de detalle de compras
CREATE TABLE compra_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de servicios
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_servicio DATE NOT NULL,
    hora_servicio TIME NOT NULL,
    numero_personas INT DEFAULT 1,
    amenidad_id INT,
    descripcion_servicio TEXT NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'rechazado', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10,2) DEFAULT 0.00,
    personal_asignado_id INT,
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (amenidad_id) REFERENCES amenidades(id),
    FOREIGN KEY (personal_asignado_id) REFERENCES usuarios(id)
);

-- Tabla de actividades/comentarios
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_solicitud ENUM('reservacion', 'compra', 'servicio') NOT NULL,
    solicitud_id INT NOT NULL,
    usuario_id INT NOT NULL,
    actividad TEXT NOT NULL,
    fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de comprobantes de pago
CREATE TABLE comprobantes_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_solicitud ENUM('reservacion', 'compra', 'servicio') NOT NULL,
    solicitud_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES
('Administrador', 'admin@reservaciones.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Comercio Demo', 'comercio@reservaciones.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comercio'),
('Cliente Demo', 'cliente@reservaciones.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

INSERT INTO amenidades (nombre, descripcion, capacidad_maxima) VALUES
('Mesa para 2', 'Mesa estándar para 2 personas', 2),
('Mesa para 4', 'Mesa estándar para 4 personas', 4),
('Mesa para 6', 'Mesa grande para 6 personas', 6),
('Sala VIP', 'Sala privada VIP para eventos especiales', 10),
('Terraza', 'Mesa en terraza al aire libre', 4);

INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Menú Ejecutivo', 'Menú completo del día', 25.99, 50),
('Bebida Premium', 'Bebidas importadas', 15.50, 30),
('Postre Especial', 'Postres de la casa', 8.75, 20),
('Combo Familiar', 'Combo para 4 personas', 85.00, 15);