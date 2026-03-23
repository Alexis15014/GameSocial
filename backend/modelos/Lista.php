<?php
/**
 * Modelo: Lista.php
 * Propósito: Gestionar las listas personalizadas de videojuegos de los usuarios.
 * Proyecto: GameSocial
 */

class Lista {

    private $conexion;

    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

    // Obtiene todas las listas de un usuario, con total de juegos y portada del primero
    public function obtenerListasUsuario($id_usuario) {
        $sql = "SELECT l.*,
                       COUNT(lv.id_videojuego) as total_juegos,
                       (SELECT v2.imagen_portada
                        FROM lista_videojuego lv2
                        JOIN videojuegos v2 ON lv2.id_videojuego = v2.id_videojuego
                        WHERE lv2.id_lista = l.id_lista
                        ORDER BY lv2.fecha_agregado ASC
                        LIMIT 1) as portada_lista
                FROM listas l
                LEFT JOIN lista_videojuego lv ON l.id_lista = lv.id_lista
                WHERE l.id_usuario = :id_usuario
                GROUP BY l.id_lista
                ORDER BY l.fecha_creacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Obtiene las listas públicas de un usuario (para ver desde otro perfil)
    public function obtenerListasPublicasUsuario($id_usuario) {
        $sql = "SELECT l.*,
                       COUNT(lv.id_videojuego) as total_juegos,
                       (SELECT v2.imagen_portada
                        FROM lista_videojuego lv2
                        JOIN videojuegos v2 ON lv2.id_videojuego = v2.id_videojuego
                        WHERE lv2.id_lista = l.id_lista
                        ORDER BY lv2.fecha_agregado ASC
                        LIMIT 1) as portada_lista
                FROM listas l
                LEFT JOIN lista_videojuego lv ON l.id_lista = lv.id_lista
                WHERE l.id_usuario = :id_usuario AND l.es_publica = 1
                GROUP BY l.id_lista
                ORDER BY l.fecha_creacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Obtiene una lista por su ID verificando que pertenece al usuario
    public function obtenerPorId($id_lista, $id_usuario) {
        $sql = "SELECT * FROM listas WHERE id_lista = :id_lista AND id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",   $id_lista,   PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene una lista pública por su ID (sin verificar propietario)
    public function obtenerPublicaPorId($id_lista) {
        $sql = "SELECT l.*, u.nombre_usuario
                FROM listas l
                JOIN usuarios u ON l.id_usuario = u.id_usuario
                WHERE l.id_lista = :id_lista AND l.es_publica = 1
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista", $id_lista, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene los videojuegos de una lista concreta
    public function obtenerJuegosLista($id_lista) {
        $sql = "SELECT v.id_videojuego, v.titulo, v.plataforma, v.genero, v.tipo, v.imagen_portada,
                       lv.fecha_agregado
                FROM lista_videojuego lv
                JOIN videojuegos v ON lv.id_videojuego = v.id_videojuego
                WHERE lv.id_lista = :id_lista
                ORDER BY lv.fecha_agregado DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista", $id_lista, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Crea una nueva lista
    public function crear($id_usuario, $nombre, $descripcion, $es_publica) {
        $nombre      = trim(substr($nombre, 0, 100));
        $descripcion = trim($descripcion);
        $es_publica  = $es_publica ? 1 : 0;

        $sql = "INSERT INTO listas (id_usuario, nombre, descripcion, es_publica)
                VALUES (:id_usuario, :nombre, :descripcion, :es_publica)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_usuario",  $id_usuario,  PDO::PARAM_INT);
        $stmt->bindValue(":nombre",      $nombre,      PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(":es_publica",  $es_publica,  PDO::PARAM_INT);
        $stmt->execute();

        return (int)$this->conexion->lastInsertId();
    }

    // Cambia la visibilidad de una lista
    public function toggleVisibilidad($id_lista, $id_usuario) {
        $sql = "UPDATE listas SET es_publica = 1 - es_publica
                WHERE id_lista = :id_lista AND id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",   $id_lista,   PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Elimina una lista (sólo si pertenece al usuario)
    public function eliminar($id_lista, $id_usuario) {
        $sql = "DELETE FROM listas WHERE id_lista = :id_lista AND id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",   $id_lista,   PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Añade un videojuego a una lista
    public function agregarJuego($id_lista, $id_videojuego) {
        $sql = "INSERT IGNORE INTO lista_videojuego (id_lista, id_videojuego) VALUES (:id_lista, :id_videojuego)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",      $id_lista,      PDO::PARAM_INT);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Quita un videojuego de una lista
    public function quitarJuego($id_lista, $id_videojuego) {
        $sql = "DELETE FROM lista_videojuego WHERE id_lista = :id_lista AND id_videojuego = :id_videojuego";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",      $id_lista,      PDO::PARAM_INT);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Comprueba si un juego ya está en una lista
    public function juegoEnLista($id_lista, $id_videojuego) {
        $sql = "SELECT 1 FROM lista_videojuego WHERE id_lista = :id_lista AND id_videojuego = :id_videojuego LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_lista",      $id_lista,      PDO::PARAM_INT);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }
}
