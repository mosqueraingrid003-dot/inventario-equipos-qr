-- =====================================================================
--  Sistema web para la gestión de inventarios de equipos de cómputo
--  Base de datos: inventario_ti   (MariaDB / MySQL)
--  Proyecto Integrador - UNIAJC - Tecnología en Sistemas de Información
--
--  Cómo usarlo: abrir en phpMyAdmin, HeidiSQL o la consola de MariaDB
--  y ejecutar TODO el archivo. Crea la base de datos, las tablas,
--  las llaves y datos de prueba.
--  Usuarios de prueba (contraseña de los tres: Admin123*):
--     admin@inventario.test   -> Administrador
--     tecnico@inventario.test -> Técnico
--     consulta@inventario.test-> Consulta
-- =====================================================================

DROP DATABASE IF EXISTS inventario_ti;
CREATE DATABASE inventario_ti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_ti;

-- ---------------------------------------------------------------------
-- 1. ROLES: perfiles de acceso (Administrador, Técnico, Consulta)
-- ---------------------------------------------------------------------
CREATE TABLE roles (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(30)  NOT NULL,
  descripcion VARCHAR(150) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. USERS: usuarios que inician sesión en el sistema
-- ---------------------------------------------------------------------
CREATE TABLE users (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(100) NOT NULL,
  email          VARCHAR(120) NOT NULL,
  password       VARCHAR(255) NOT NULL,            -- contraseña cifrada (hash)
  role_id        BIGINT UNSIGNED NOT NULL,
  activo         TINYINT(1)   NOT NULL DEFAULT 1,
  remember_token VARCHAR(100) NULL,
  created_at     TIMESTAMP NULL DEFAULT NULL,
  updated_at     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. TIPOS_EQUIPO: catálogo (portátil, escritorio, impresora, ...)
-- ---------------------------------------------------------------------
CREATE TABLE tipos_equipo (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre     VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tipos_equipo_nombre (nombre)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. UBICACIONES: sedes y dependencias donde están los equipos
-- ---------------------------------------------------------------------
CREATE TABLE ubicaciones (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(80)  NOT NULL,               -- ej. Sala de sistemas 1
  sede        VARCHAR(80)  NOT NULL,               -- ej. Sede principal
  descripcion VARCHAR(200) NULL,
  created_at  TIMESTAMP NULL DEFAULT NULL,
  updated_at  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ubicaciones_sede_nombre (sede, nombre)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. RESPONSABLES: personas a quienes se asignan los equipos
--    (datos personales: acceso restringido por rol, Ley 1581 de 2012)
-- ---------------------------------------------------------------------
CREATE TABLE responsables (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_completo VARCHAR(120) NOT NULL,
  identificacion  VARCHAR(20)  NOT NULL,
  cargo           VARCHAR(80)  NULL,
  dependencia     VARCHAR(80)  NULL,
  correo          VARCHAR(120) NULL,
  telefono        VARCHAR(20)  NULL,
  activo          TINYINT(1)   NOT NULL DEFAULT 1,
  created_at      TIMESTAMP NULL DEFAULT NULL,
  updated_at      TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_responsables_identificacion (identificacion)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. EQUIPOS: tabla central del inventario
-- ---------------------------------------------------------------------
CREATE TABLE equipos (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_inventario VARCHAR(20)  NOT NULL,         -- ej. EQ-0001 (va en el QR)
  numero_serie      VARCHAR(60)  NOT NULL,
  marca             VARCHAR(50)  NOT NULL,
  modelo            VARCHAR(80)  NOT NULL,
  tipo_equipo_id    BIGINT UNSIGNED NOT NULL,
  ubicacion_id      BIGINT UNSIGNED NOT NULL,
  responsable_id    BIGINT UNSIGNED NULL,          -- puede no tener responsable
  fecha_adquisicion DATE NOT NULL,
  estado            ENUM('Activo','En mantenimiento','Dado de baja') NOT NULL DEFAULT 'Activo',
  especificaciones  VARCHAR(255) NULL,             -- ej. i5, 8 GB RAM, 256 GB SSD
  observaciones     TEXT NULL,
  created_at        TIMESTAMP NULL DEFAULT NULL,
  updated_at        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_equipos_codigo (codigo_inventario),
  UNIQUE KEY uq_equipos_serie (numero_serie),
  KEY idx_equipos_estado (estado),
  CONSTRAINT fk_equipos_tipo FOREIGN KEY (tipo_equipo_id) REFERENCES tipos_equipo (id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_equipos_ubicacion FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones (id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_equipos_responsable FOREIGN KEY (responsable_id) REFERENCES responsables (id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. MANTENIMIENTOS: historial de mantenimientos y fallas por equipo
-- ---------------------------------------------------------------------
CREATE TABLE mantenimientos (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  equipo_id   BIGINT UNSIGNED NOT NULL,
  user_id     BIGINT UNSIGNED NOT NULL,            -- técnico que lo registró
  fecha       DATE NOT NULL,
  tipo        ENUM('Preventivo','Correctivo') NOT NULL,
  es_falla    TINYINT(1) NOT NULL DEFAULT 0,       -- 1 = el evento fue una falla
  descripcion TEXT NOT NULL,
  costo       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at  TIMESTAMP NULL DEFAULT NULL,
  updated_at  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_mant_equipo_fecha (equipo_id, fecha),
  CONSTRAINT chk_mant_costo CHECK (costo >= 0),
  CONSTRAINT fk_mant_equipo FOREIGN KEY (equipo_id) REFERENCES equipos (id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_mant_user FOREIGN KEY (user_id) REFERENCES users (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. PARAMETROS: valores configurables de la regla de obsolescencia
-- ---------------------------------------------------------------------
CREATE TABLE parametros (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave       VARCHAR(50)  NOT NULL,
  valor       VARCHAR(50)  NOT NULL,
  descripcion VARCHAR(200) NULL,
  created_at  TIMESTAMP NULL DEFAULT NULL,
  updated_at  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_parametros_clave (clave)
) ENGINE=InnoDB;

-- =====================================================================
--  DATOS DE PRUEBA (todos ficticios)
-- =====================================================================

INSERT INTO roles (id, nombre, descripcion) VALUES
  (1, 'Administrador', 'Gestiona usuarios, catálogos, equipos, parámetros y reportes'),
  (2, 'Técnico',       'Registra y actualiza equipos y mantenimientos'),
  (3, 'Consulta',      'Solo consulta equipos y reportes');

INSERT INTO users (id, name, email, password, role_id, activo, created_at, updated_at) VALUES
  (1, 'Administrador del sistema', 'admin@inventario.test',    '$2y$10$rUW8jRCYEM9kIAXCKffIgu6Wfs8ymWIWnS6xpyiUNDywHm0OdVZoK', 1, 1, NOW(), NOW()),
  (2, 'Técnico de soporte',        'tecnico@inventario.test',  '$2y$10$rUW8jRCYEM9kIAXCKffIgu6Wfs8ymWIWnS6xpyiUNDywHm0OdVZoK', 2, 1, NOW(), NOW()),
  (3, 'Usuario de consulta',       'consulta@inventario.test', '$2y$10$rUW8jRCYEM9kIAXCKffIgu6Wfs8ymWIWnS6xpyiUNDywHm0OdVZoK', 3, 1, NOW(), NOW());

INSERT INTO tipos_equipo (id, nombre, created_at, updated_at) VALUES
  (1, 'Portátil',      NOW(), NOW()),
  (2, 'Escritorio',    NOW(), NOW()),
  (3, 'Todo en uno',   NOW(), NOW()),
  (4, 'Monitor',       NOW(), NOW()),
  (5, 'Impresora',     NOW(), NOW()),
  (6, 'Servidor',      NOW(), NOW());

INSERT INTO ubicaciones (id, nombre, sede, descripcion, created_at, updated_at) VALUES
  (1, 'Sala de sistemas 1', 'Sede principal', 'Laboratorio de cómputo, primer piso',  NOW(), NOW()),
  (2, 'Sala de sistemas 2', 'Sede principal', 'Laboratorio de cómputo, segundo piso', NOW(), NOW()),
  (3, 'Administración',     'Sede principal', 'Oficinas administrativas',              NOW(), NOW()),
  (4, 'Biblioteca',         'Sede principal', 'Equipos de consulta',                   NOW(), NOW()),
  (5, 'Recepción',          'Sede norte',     'Atención al público',                   NOW(), NOW()),
  (6, 'Bodega de TI',       'Sede principal', 'Equipos en reserva o en revisión',      NOW(), NOW());

INSERT INTO responsables (id, nombre_completo, identificacion, cargo, dependencia, correo, telefono, activo, created_at, updated_at) VALUES
  (1, 'Laura Gómez Prado',    '1000000001', 'Coordinadora administrativa', 'Administración', 'laura.gomez@ejemplo.test',  '3000000001', 1, NOW(), NOW()),
  (2, 'Carlos Rivera Soto',   '1000000002', 'Auxiliar contable',           'Administración', 'carlos.rivera@ejemplo.test','3000000002', 1, NOW(), NOW()),
  (3, 'Marta Ortiz León',     '1000000003', 'Bibliotecaria',               'Biblioteca',     'marta.ortiz@ejemplo.test',  '3000000003', 1, NOW(), NOW()),
  (4, 'Jorge Valencia Cruz',  '1000000004', 'Recepcionista',               'Recepción',      'jorge.valencia@ejemplo.test','3000000004', 1, NOW(), NOW()),
  (5, 'Diana Reyes Mora',     '1000000005', 'Docente',                     'Sistemas',       'diana.reyes@ejemplo.test',  '3000000005', 1, NOW(), NOW()),
  (6, 'Pedro Salazar Díaz',   '1000000006', 'Auxiliar de sistemas',        'Sistemas',       'pedro.salazar@ejemplo.test','3000000006', 1, NOW(), NOW());

INSERT INTO equipos (id, codigo_inventario, numero_serie, marca, modelo, tipo_equipo_id, ubicacion_id, responsable_id, fecha_adquisicion, estado, especificaciones, observaciones, created_at, updated_at) VALUES
  (1,  'EQ-0001', 'SN-HP-0001',   'HP',     'ProDesk 400 G4',      2, 1, NULL, '2018-03-12', 'Activo',           'Core i5, 8 GB RAM, 500 GB HDD', 'Equipo de laboratorio',          NOW(), NOW()),
  (2,  'EQ-0002', 'SN-HP-0002',   'HP',     'ProDesk 400 G4',      2, 1, NULL, '2018-03-12', 'Activo',           'Core i5, 8 GB RAM, 500 GB HDD', 'Equipo de laboratorio',          NOW(), NOW()),
  (3,  'EQ-0003', 'SN-DELL-0003', 'Dell',   'OptiPlex 3060',       2, 2, NULL, '2019-08-20', 'Activo',           'Core i5, 8 GB RAM, 256 GB SSD', 'Equipo de laboratorio',          NOW(), NOW()),
  (4,  'EQ-0004', 'SN-DELL-0004', 'Dell',   'OptiPlex 3060',       2, 2, NULL, '2019-08-20', 'En mantenimiento', 'Core i5, 8 GB RAM, 256 GB SSD', 'Presenta fallas recurrentes', NOW(), NOW()),
  (5,  'EQ-0005', 'SN-LEN-0005',  'Lenovo', 'ThinkPad E14',        1, 3, 1,    '2022-02-15', 'Activo',           'Core i5, 16 GB RAM, 512 GB SSD', NULL,                    NOW(), NOW()),
  (6,  'EQ-0006', 'SN-LEN-0006',  'Lenovo', 'ThinkPad E14',        1, 3, 2,    '2022-02-15', 'Activo',           'Core i5, 16 GB RAM, 512 GB SSD', NULL,                    NOW(), NOW()),
  (7,  'EQ-0007', 'SN-ASU-0007',  'Asus',   'VivoBook 15',         1, 4, 3,    '2023-06-01', 'Activo',           'Ryzen 5, 8 GB RAM, 512 GB SSD', NULL,                     NOW(), NOW()),
  (8,  'EQ-0008', 'SN-HP-0008',   'HP',     'All-in-One 22',       3, 5, 4,    '2021-11-09', 'Activo',           'Core i3, 8 GB RAM, 256 GB SSD', NULL,                     NOW(), NOW()),
  (9,  'EQ-0009', 'SN-LG-0009',   'LG',     '24MK430H',            4, 1, NULL, '2020-01-10', 'Activo',           'Monitor 24 pulgadas',           NULL,                     NOW(), NOW()),
  (10, 'EQ-0010', 'SN-EPS-0010',  'Epson',  'L3150',               5, 3, 1,    '2019-05-05', 'Activo',           'Multifuncional de tinta',       NULL,                     NOW(), NOW()),
  (11, 'EQ-0011', 'SN-DELL-0011', 'Dell',   'PowerEdge T140',      6, 6, 6,    '2020-09-30', 'Activo',           'Xeon, 32 GB RAM, 2 TB',         'Servidor de archivos',   NOW(), NOW()),
  (12, 'EQ-0012', 'SN-ACE-0012',  'Acer',   'Aspire 3',            1, 6, NULL, '2017-04-18', 'Dado de baja',     'Core i3, 4 GB RAM, 500 GB HDD', 'Dado de baja por daño',  NOW(), NOW()),
  (13, 'EQ-0013', 'SN-LEN-0013',  'Lenovo', 'ThinkCentre M70s',    2, 3, 5,    '2024-03-22', 'Activo',           'Core i5, 16 GB RAM, 512 GB SSD', NULL,                    NOW(), NOW()),
  (14, 'EQ-0014', 'SN-HP-0014',   'HP',     'Laptop 15-fd',        1, 2, 5,    '2025-07-14', 'Activo',           'Core i5, 16 GB RAM, 512 GB SSD', NULL,                    NOW(), NOW());

INSERT INTO mantenimientos (equipo_id, user_id, fecha, tipo, es_falla, descripcion, costo, created_at, updated_at) VALUES
  (4, 2, '2026-01-20', 'Correctivo',  1, 'Falla de la fuente de poder; se reemplazó',          85000.00, NOW(), NOW()),
  (4, 2, '2026-04-08', 'Correctivo',  1, 'El equipo no enciende; falla de la tarjeta madre',   0.00,     NOW(), NOW()),
  (4, 2, '2026-09-02', 'Correctivo',  1, 'Reinicios constantes; sobrecalentamiento',           30000.00, NOW(), NOW()),
  (1, 2, '2026-02-10', 'Preventivo',  0, 'Limpieza interna y cambio de pasta térmica',         20000.00, NOW(), NOW()),
  (2, 2, '2026-02-10', 'Preventivo',  0, 'Limpieza interna y cambio de pasta térmica',         20000.00, NOW(), NOW()),
  (3, 2, '2026-06-18', 'Correctivo',  1, 'Disco duro lento; se cambió por SSD',                120000.00,NOW(), NOW()),
  (10,2, '2026-03-03', 'Correctivo',  1, 'Cabezal obstruido; limpieza',                        15000.00, NOW(), NOW()),
  (5, 2, '2026-07-21', 'Preventivo',  0, 'Actualización de sistema y limpieza',                0.00,     NOW(), NOW()),
  (12,2, '2025-10-05', 'Correctivo',  1, 'Pantalla dañada; no se justifica la reparación',     0.00,     NOW(), NOW());

INSERT INTO parametros (clave, valor, descripcion, created_at, updated_at) VALUES
  ('vida_util_anios', '5',  'Antigüedad (en años) a partir de la cual un equipo se marca como obsoleto', NOW(), NOW()),
  ('limite_fallas',   '3',  'Número de fallas en la ventana de meses que marca un equipo como obsoleto',  NOW(), NOW()),
  ('ventana_meses',   '12', 'Ventana de meses para contar las fallas recientes',                          NOW(), NOW());

-- =====================================================================
--  CONSULTAS DE PRUEBA
-- =====================================================================

-- A) Equipos con alerta de obsolescencia (antigüedad >= 5 años o >= 3 fallas en 12 meses)
SELECT t.codigo_inventario, t.marca, t.modelo, t.antiguedad_anios, t.fallas_12m
FROM (
  SELECT e.codigo_inventario, e.marca, e.modelo,
         TIMESTAMPDIFF(YEAR, e.fecha_adquisicion, CURDATE()) AS antiguedad_anios,
         (SELECT COUNT(*) FROM mantenimientos m
           WHERE m.equipo_id = e.id AND m.es_falla = 1
             AND m.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)) AS fallas_12m
  FROM equipos e
  WHERE e.estado <> 'Dado de baja'
) t
WHERE t.antiguedad_anios >= 5 OR t.fallas_12m >= 3
ORDER BY t.antiguedad_anios DESC;

-- B) Existencias por ubicación y estado
SELECT u.sede, u.nombre AS ubicacion, e.estado, COUNT(*) AS total
FROM equipos e
JOIN ubicaciones u ON u.id = e.ubicacion_id
GROUP BY u.sede, u.nombre, e.estado
ORDER BY u.sede, u.nombre;
