CREATE DATABASE IF NOT EXISTS mensajeria
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mensajeria;

-- Tabla de usuarios (sustituye a datos/usuarios.txt)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'repartidor', 'admin') NOT NULL
);

-- Tabla de envíos (sustituye a datos/envios.txt)
CREATE TABLE envios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(50) NOT NULL,
    dir_recogida VARCHAR(200) NOT NULL,
    dir_entrega VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    estado ENUM('En espera', 'En reparto', 'Entregado') NOT NULL DEFAULT 'En espera',
    FOREIGN KEY (cliente) REFERENCES usuarios(usuario) ON DELETE CASCADE
);