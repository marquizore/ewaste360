-- =========================================================================
-- BASE DE DATOS: E-WASTE 360° (Gestión Sustentable de Residuos Electrónicos)
-- =========================================================================
CREATE DATABASE IF NOT EXISTS ewaste360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ewaste360;

DROP TABLE IF EXISTS participaciones;
DROP TABLE IF EXISTS campanas;
DROP TABLE IF EXISTS movimientos;
DROP TABLE IF EXISTS diagnosticos;
DROP TABLE IF EXISTS equipos;
DROP TABLE IF EXISTS usuarios;

-- 1. Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('visitante', 'capturista', 'responsable_ambiental', 'administrador') NOT NULL DEFAULT 'visitante',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Equipos Electrónicos (Inventario RAEE)
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_inventario VARCHAR(30) NOT NULL UNIQUE,
    categoria ENUM('Computadora', 'Laptop', 'Monitor', 'Impresora', 'Periferico', 'Telefonia', 'Bateria_Cable', 'Otro') NOT NULL,
    marca VARCHAR(60) NOT NULL,
    modelo VARCHAR(80) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    condicion_general VARCHAR(255) NOT NULL,
    estado_clasificacion ENUM('funcional', 'reparable', 'reutilizable', 'gestion_especializada') NOT NULL,
    ubicacion_actual VARCHAR(100) NOT NULL DEFAULT 'Almacén Temporal RAEE',
    registrado_por INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipos_usuario FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Tabla de Diagnósticos y Reparaciones
CREATE TABLE diagnosticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    falla_detectada TEXT NOT NULL,
    accion_reparacion TEXT NOT NULL,
    costo_reparacion DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ahorro_estimado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    decision_final ENUM('reutilizar', 'conservar', 'gestion_especializada') NOT NULL,
    area_reasignada VARCHAR(100) DEFAULT NULL,
    fecha_diagnostico DATE NOT NULL,
    responsable_id INT NOT NULL,
    CONSTRAINT fk_diag_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    CONSTRAINT fk_diag_responsable FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- 4. Tabla de Movimientos y Trazabilidad de Destino
CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tipo_movimiento ENUM('ingreso', 'diagnostico', 'reparacion', 'reasignacion', 'entrega_gestor') NOT NULL,
    destino_documentado VARCHAR(150) NOT NULL,
    gestor_verificado TINYINT(1) NOT NULL DEFAULT 0,
    observaciones TEXT,
    fecha_movimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    responsable_id INT NOT NULL,
    CONSTRAINT fk_mov_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    CONSTRAINT fk_mov_responsable FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- 5. Tabla de Campañas de Recolección
CREATE TABLE campanas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    ubicacion VARCHAR(150) NOT NULL,
    equipos_aceptados VARCHAR(255) NOT NULL,
    estado ENUM('programada', 'activa', 'finalizada') NOT NULL DEFAULT 'activa',
    creado_por INT NOT NULL,
    CONSTRAINT fk_campana_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- 6. Tabla de Participaciones en Campañas
CREATE TABLE participaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campana_id INT NOT NULL,
    usuario_id INT DEFAULT NULL,
    nombre_participante VARCHAR(100) NOT NULL,
    tipo_residuo_entregado VARCHAR(120) NOT NULL,
    cantidad_unidades INT NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_part_campana FOREIGN KEY (campana_id) REFERENCES campanas(id) ON DELETE CASCADE,
    CONSTRAINT fk_part_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================================
-- USUARIOS DE PRUEBA (Contraseña para todos los roles demo: Eco2026*)
-- =========================================================================
INSERT INTO usuarios (nombre, username, email, password_hash, rol, activo) VALUES
('Marco Uriel Reyes (Administrador)', 'admin', 'admin@ewaste360.edu.mx', '$2y$12$gO6LMGV/I3FFFD8Q5EfXTO45lzkHIF02.TnZzOlKc3ZqE/RP9tJKq', 'administrador', 1),
('Meredith Franco (Resp. Ambiental)', 'ambiental', 'ambiental@ewaste360.edu.mx', '$2y$12$gO6LMGV/I3FFFD8Q5EfXTO45lzkHIF02.TnZzOlKc3ZqE/RP9tJKq', 'responsable_ambiental', 1),
('Jeremhy Ríos (Capturista)', 'capturista', 'captura@ewaste360.edu.mx', '$2y$12$gO6LMGV/I3FFFD8Q5EfXTO45lzkHIF02.TnZzOlKc3ZqE/RP9tJKq', 'capturista', 1),
('Juan Carlos Muñoz (Visitante)', 'visitante', 'visitante@ewaste360.edu.mx', '$2y$12$gO6LMGV/I3FFFD8Q5EfXTO45lzkHIF02.TnZzOlKc3ZqE/RP9tJKq', 'visitante', 1);

INSERT INTO equipos (codigo_inventario, categoria, marca, modelo, fecha_ingreso, condicion_general, estado_clasificacion, ubicacion_actual, registrado_por) VALUES
('RAEE-2026-001', 'Computadora', 'Dell', 'OptiPlex 7050', '2026-10-01', 'Enciende correctamente, requiere cambio de disco mecánico a SSD', 'reparable', 'Taller de Mantenimiento', 3),
('RAEE-2026-002', 'Monitor', 'HP', 'ProDisplay P223', '2026-10-03', 'Funcionando al 100%, sin píxeles muertos', 'reutilizable', 'Laboratorio de Redes', 3),
('RAEE-2026-003', 'Impresora', 'Epson', 'EcoTank L3150', '2026-10-05', 'Tarjeta lógica quemada y cabezal obstruido irreversiblemente', 'gestion_especializada', 'Almacén RAEE Peligroso', 3),
('RAEE-2026-004', 'Laptop', 'Lenovo', 'ThinkPad T480', '2026-10-10', 'Operativa tras limpieza interna y cambio de pasta térmica', 'funcional', 'Centro de Cómputo Sala B', 3),
('RAEE-2026-005', 'Bateria_Cable', 'APC', 'Batería UPS 12V 7Ah', '2026-10-12', 'Batería agotada sin retención de carga, requiere manejo controlado', 'gestion_especializada', 'Contenedor de Residuos Especiales', 3);

INSERT INTO diagnosticos (equipo_id, falla_detectada, accion_reparacion, costo_reparacion, ahorro_estimado, decision_final, area_reasignada, fecha_diagnostico, responsable_id) VALUES
(1, 'Lentitud extrema por disco duro con sectores dañados', 'Instalación de SSD 480GB y 8GB RAM reciclada', 650.00, 7500.00, 'reutilizar', 'Laboratorio de Sistemas', '2026-10-02', 2),
(2, 'Ninguna falla operativa', 'Limpieza preventiva de carcasa y puertos', 50.00, 2800.00, 'reutilizar', 'Laboratorio de Redes', '2026-10-04', 2),
(3, 'Cortocircuito en fuente y placa principal', 'Extracción de motores paso a paso para robótica y envío de carcasa a gestor', 0.00, 450.00, 'gestion_especializada', 'Recicladora Electrónica del Bajío S.A.', '2026-10-06', 2);

INSERT INTO movimientos (equipo_id, tipo_movimiento, destino_documentado, gestor_verificado, observaciones, responsable_id) VALUES
(1, 'reparacion', 'Laboratorio de Sistemas', 1, 'Equipo actualizado con SSD y reasignado para prácticas de programación', 2),
(2, 'reasignacion', 'Laboratorio de Redes', 1, 'Reutilización directa en estación de trabajo #14', 2),
(3, 'entrega_gestor', 'Recicladora Electrónica del Bajío S.A. (Manifiesto #MAN-2026-88)', 1, 'Gestor verificado ante padrón ambiental estatal', 2),
(5, 'ingreso', 'Contenedor de Residuos Especiales', 0, 'Pendiente de recolección por gestor autorizado de baterías', 3);

INSERT INTO campanas (nombre, descripcion, fecha_inicio, fecha_fin, ubicacion, equipos_aceptados, estado, creado_por) VALUES
('Reciclatón Tecnológico Otoño 2026', 'Campaña institucional para acopio de periféricos, laptops, cables y baterías en desuso.', '2026-10-15', '2026-12-15', 'Explanada Principal Edificio de Sistemas', 'Computadoras, Monitores, Teclados, Cables, Celulares y Baterías', 'activa', 2),
('Jornada de Reparación y Reuso Escolar', 'Trae tu equipo lento o con fallas menores para diagnóstico y reacondicionamiento comunitario.', '2026-11-10', '2026-12-20', 'Laboratorio de Hardware y Microcontroladores', 'Laptops, PC de escritorio, Monitores', 'activa', 2);

INSERT INTO participaciones (campana_id, usuario_id, nombre_participante, tipo_residuo_entregado, cantidad_unidades) VALUES
(1, 2, 'Meredith Alejandra Franco Monroy', 'Teclados y cables USB varios', 5),
(1, 4, 'Juan Carlos Muñoz Martínez', 'Monitor LCD 19 pulgadas y ratón óptico', 2),
(1, 3, 'Jeremhy Damián Ríos López', 'Baterías de laptop y cargadores en desuso', 4),
(2, NULL, 'Paulina Isabel Noriega Mireles', 'Laptop con pantalla funcional para diagnóstico', 1);
