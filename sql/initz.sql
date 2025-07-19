
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS revolucion_movil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE revolucion_movil;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') DEFAULT 'empleado',
    activo BOOLEAN DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías (para accesorios)
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla de accesorios
CREATE TABLE IF NOT EXISTS accesorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    marca VARCHAR(100),
    categoria_id INT NULL,
    modelo VARCHAR(100),
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    precio_compra DECIMAL(10,2) DEFAULT 0,
    precio_venta DECIMAL(10,2) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    activacion DECIMAL(10,2) DEFAULT 0,
    software DECIMAL(10,2) DEFAULT 0,
    frp DECIMAL(10,2) DEFAULT 0,
    formatear DECIMAL(10,2) DEFAULT 0,
    pin_de_carga DECIMAL(10,2) DEFAULT 0,
    letras_rojas BOOLEAN DEFAULT 0,
    pegado_tapa BOOLEAN DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de movimientos (servicios y accesorios)
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('entrada', 'salida', 'servicio') NOT NULL,
    sector ENUM('accesorio', 'servicio') NOT NULL,
    item_id INT NOT NULL,
    cantidad INT NOT NULL,
    observacion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
