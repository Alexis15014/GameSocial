<?php
/**
 * Modelo: PostRespuesta.php
 * Propósito: Gestionar los comentarios y respuestas anidadas dentro de los posts del feed.
 * Proyecto: GameSocial
 */

class PostRespuesta {

    private $conexion;

	// Instanciamos a la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

    // Insertamos una respuesta a un post o una respuesta a otra respuesta.
    public function insertar($id_post, $id_usuario, $contenido, $id_respuesta_padre = null) {
        $sql = "INSERT INTO post_respuestas (id_post, id_usuario, contenido, id_respuesta_padre) 
                VALUES (:id_post, :id_usuario, :contenido, :id_respuesta_padre)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_post", $id_post, PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":contenido", $contenido, PDO::PARAM_STR);

        if ($id_respuesta_padre === null) {
            $stmt->bindValue(":id_respuesta_padre", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":id_respuesta_padre", (int)$id_respuesta_padre, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }

    // Obtenemos todas las respuestas de un post y las organiza jerárquicamente.
    public function obtenerPorPost($id_post) {
        $sql = "SELECT r.*, u.nombre_usuario, u.foto_perfil
                FROM post_respuestas r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_post = :id_post
                ORDER BY r.fecha_creacion ASC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_post", $id_post, PDO::PARAM_INT);
        $stmt->execute();
        
        $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $this->organizarHilos($respuestas);
    }

    // Algoritmo para organizar las respuestas en forma de árbol.
    private function organizarHilos($respuestas) {
        $map = [];
        $roots = [];

        // Indexar por ID
        foreach ($respuestas as $r) {
            $r['respuestas'] = [];
            $map[$r['id_respuesta']] = $r;
        }

        // Construir jerarquía
        foreach ($map as $id => &$r) {
            if ($r['id_respuesta_padre']) {
                $padre_id = $r['id_respuesta_padre'];
                if (isset($map[$padre_id])) {
                    $map[$padre_id]['respuestas'][] = &$r;
                } else {
                    // Si el padre no existe (por borrado), lo tratamos como raíz
                    $roots[] = &$r;
                }
            } else {
                $roots[] = &$r;
            }
        }

        return $roots;
    }

    // Eliminamos una respuesta específica.
    public function eliminar($id) {
        $sql = "DELETE FROM post_respuestas WHERE id_respuesta = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}