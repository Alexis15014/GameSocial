-- ============================================================
-- MIGRACIÓN GameSocial
-- Nuevos estados de juego + Sistema de Listas de Videojuegos
-- ============================================================

-- 1. Modificar la columna 'estado' en usuario_videojuego
--    para aceptar los nuevos valores en español
ALTER TABLE usuario_videojuego
    MODIFY COLUMN estado ENUM(
        'sin_estado',
        'sin_iniciar',
        'inacabado',
        'terminado',
        'completado',
        'continuo',
        'abandonado'
    ) NOT NULL DEFAULT 'sin_estado';

-- 2. Migrar registros anteriores al nuevo esquema
UPDATE usuario_videojuego SET estado = 'sin_iniciar'  WHERE estado = 'pendiente';
UPDATE usuario_videojuego SET estado = 'inacabado'    WHERE estado = 'en_progreso';
UPDATE usuario_videojuego SET estado = 'terminado'    WHERE estado = 'finalizado';

-- 3. Crear tabla de listas de videojuegos
CREATE TABLE IF NOT EXISTS listas (
    id_lista         INT          NOT NULL AUTO_INCREMENT,
    id_usuario       INT          NOT NULL,
    nombre           VARCHAR(100) NOT NULL,
    descripcion      TEXT         DEFAULT NULL,
    es_publica       TINYINT(1)   NOT NULL DEFAULT 1,
    fecha_creacion   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_lista),
    INDEX idx_usuario (id_usuario),
    CONSTRAINT fk_lista_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla intermedia lista_videojuego
CREATE TABLE IF NOT EXISTS lista_videojuego (
    id_lista       INT      NOT NULL,
    id_videojuego  INT      NOT NULL,
    fecha_agregado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_lista, id_videojuego),
    CONSTRAINT fk_lv_lista FOREIGN KEY (id_lista)
        REFERENCES listas(id_lista) ON DELETE CASCADE,
    CONSTRAINT fk_lv_juego FOREIGN KEY (id_videojuego)
        REFERENCES videojuegos(id_videojuego) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
