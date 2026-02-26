<?php
/**
 * Modelo: Comentario.php
 * Propósito: Gestionar los comentarios y respuestas de los videojuegos.
 * Proyecto: GameSocial
 */

class Comentario {

    private $conexion;

    // Instanciamos a la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

    // Obtenemos todos los comentarios de un juego y los organizamos en forma de árbol.
    public function obtenerPorVideojuego($id_videojuego) {
        $sql = "SELECT 
                    c.id_comentario,
                    c.id_usuario,
                    c.contenido,
                    c.fecha_comentario,
                    c.id_comentario_padre,
                    u.nombre_usuario,
                    u.foto_perfil,
                    (
                        SELECT COUNT(*)
                        FROM likes l
                        WHERE l.tipo = 'comentario'
                        AND l.id_objetivo = c.id_comentario
                    ) AS likes
                FROM comentarios c
                JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_videojuego = :id_videojuego
                ORDER BY c.fecha_comentario ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- LÓGICA DE ORGANIZACIÓN EN ÁRBOLES ---
        $arbol = [];
        $map = [];

        // Primero creamos un mapa indexado por ID
        foreach ($comentarios as $c) {
            $c['respuestas'] = [];
            $map[$c['id_comentario']] = $c;
        }

        // Luego vinculamos cada hijo con su respectivo padre
        foreach ($map as $id => $comentario) {
            if ($comentario['id_comentario_padre'] === null) {
                // Es un comentario raíz (hilo principal)
                $arbol[] = &$map[$id];
            } else {
                // Es una respuesta a otro comentario
                if (isset($map[$comentario['id_comentario_padre']])) {
                    $map[$comentario['id_comentario_padre']]['respuestas'][] = &$map[$id];
                }
            }
        }
        return $arbol;
    }

    // Insertamos un nuevo comentario o una respuesta.
    public function insertar($id_usuario, $id_videojuego, $contenido, $id_comentario_padre = null) {
        $sql = "INSERT INTO comentarios (id_usuario, id_videojuego, contenido, id_comentario_padre) 
                VALUES (:id_usuario, :id_videojuego, :contenido, :padre)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->bindParam(":contenido", $contenido, PDO::PARAM_STR);
        $stmt->bindParam(":padre", $id_comentario_padre, PDO::PARAM_INT);
        
        return $stmt->execute();
    }


    // Obtenemos el ID del autor de un comentario específico.
    public function obtenerAutor($id_comentario) {
        $sql = "SELECT id_usuario FROM comentarios WHERE id_comentario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_comentario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    // Contamos el total de comentarios realizados por un usuario (estadísticas).
    public function contarComentariosUsuario($id_usuario) {
        $sql = "SELECT COUNT(*) FROM comentarios WHERE id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }
}