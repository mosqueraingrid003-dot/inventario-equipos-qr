-- Sistema web para la gestión de inventarios de equipos de cómputo con códigos QR
-- Script de base de datos (MariaDB) - Proyecto Integrador, UNIAJC
-- Autores: Andersson Sinisterra Angulo, Ingrid Vanessa Mosquera Mosquera

DROP DATABASE IF EXISTS inventario_equipos;
CREATE DATABASE inventario_equipos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_equipos;

-- Roles: Administrador, Técnico, Consulta
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
) ENGINE=InnoDB;

CREATE TABLE ubicaciones (
    id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE responsables (
    id_responsable INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(80),
    dependencia VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE equipos (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    serie VARCHAR(60) NOT NULL UNIQUE,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    tipo VARCHAR(40) NOT NULL,
    fecha_adquisicion DATE NOT NULL,
    estado ENUM('Activo','En mantenimiento','Dado de baja') NOT NULL DEFAULT 'Activo',
    id_ubicacion INT NOT NULL,
    id_responsable INT NULL,
    FOREIGN KEY (id_ubicacion) REFERENCES ubicaciones(id_ubicacion),
    FOREIGN KEY (id_responsable) REFERENCES responsables(id_responsable)
) ENGINE=InnoDB;

CREATE TABLE mantenimientos (
    id_mantenimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha DATE NOT NULL,
    tipo ENUM('Preventivo','Correctivo','Falla') NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- Datos de prueba
INSERT INTO roles (nombre) VALUES ('Administrador'), ('Técnico'), ('Consulta');

INSERT INTO ubicaciones (nombre, descripcion) VALUES
('Sala de sistemas 1', 'Primer piso'),
('Oficina administrativa', 'Segundo piso'),
('Bodega de TI', 'Equipos en reserva');

INSERT INTO responsables (nombre, cargo, dependencia) VALUES
('Responsable de prueba 1', 'Coordinador', 'Administración'),
('Responsable de prueba 2', 'Docente', 'Sistemas');

INSERT INTO equipos (serie, marca, modelo, tipo, fecha_adquisicion, estado, id_ubicacion, id_responsable) VALUES
('SN-0001', 'Dell', 'Optiplex 3080', 'Escritorio', '2020-03-15', 'Activo', 1, 2),
('SN-0002', 'HP', 'ProBook 450', 'Portátil', '2023-06-01', 'Activo', 2, 1),
('SN-0003', 'Lenovo', 'ThinkCentre M70', 'Escritorio', '2019-01-20', 'En mantenimiento', 3, NULL);

-- Los usuarios se crean desde la aplicación (la contraseña debe guardarse cifrada).
