-- Crear base de datos (si aún no existe)
CREATE DATABASE IF NOT EXISTS revolucion_movil CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE stockapp;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') DEFAULT 'empleado',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuario administrador inicial (contraseña: admin123)
INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('Administrador', 'admin@stock.com', 
    '$2y$10$Pql0i4GvMCY4Yz1V5QlAQOZTAvq7Ng.FS7g3M4.D5lz6Cd4L5VrE6', 'admin');

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla de artículos
CREATE TABLE IF NOT EXISTS accesorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    marca VARCHAR(100) NULL,
    precio DECIMAL(10, 2)NULL,
    categoria_id INT NOT NULL,
    ubicacion ENUM('tienda', 'deposito') NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
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

-- Tabla de movimientos (entradas/salidas)
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('entrada', 'salida', 'servicio') NOT NULL,
    sector ENUM('accesorio', 'servicio') NOT NULL,
    item_id INT NOT NULL,  -- Puede ser ID de accesorio o servicio
    cantidad INT NOT NULL,
    observacion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);