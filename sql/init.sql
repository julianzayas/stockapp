-- Crear base de datos (si aún no existe)
CREATE DATABASE IF NOT EXISTS stockapp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
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
    descripcion TEXT NOT NULL,
    marca VARCHAR(100) NOT NOT,
    precio DECIMAL(10, 2)NULL,
    categoria_id INT NOT NULL,
    ubicacion ENUM('tienda', 'deposito') NOT NULL,
    stock_incial INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla de movimientos (entradas/salidas)
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accesorio_id INT NOT NULL,
    tipo ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    observacion TEXT,
    FOREIGN KEY (accesorio_id) REFERENCES accesorios(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);