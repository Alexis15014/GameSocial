<?php
/**
 * Modelo: Post.php
 * Propósito: Gestionar las publicaciones del muro global/feed.
 * Proyecto: GameSocial
 */

class Post {

    private $conexion;

	// Instanciamos a la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

	// Creamos una nueva publicación en el feed.
    public function insertar($id_usuario, $contenido) {
        $sql = "INSERT INTO posts (id_usuario, contenido) 
                VALUES (:id_usuario, :contenido)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":contenido", $contenido, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    // Obtenemos todos los posts con el nombre del autor y el conteo de likes.
    public function obtenerTodos() {
        $sql = "SELECT 
                    p.*, 
                    u.nombre_usuario,
                    u.foto_perfil,
                    (
                        SELECT COUNT(*)
                        FROM likes l
                        WHERE l.tipo = 'post'
                        AND l.id_objetivo = p.id_post
                    ) AS likes
                FROM posts p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                ORDER BY p.fecha_creacion DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

	// Obtenemos el ID del propietario de un post.
    public function obtenerAutor($id_post) {
        $sql = "SELECT id_usuario FROM posts WHERE id_post = :id_post LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_post', $id_post, PDO::PARAM_INT);
        $stmt->execute();
        
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : false;
    }

    // Eliminamos una publicación de la base de datos.
    public function eliminar($id) {
        $sql = "DELETE FROM posts WHERE id_post = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}