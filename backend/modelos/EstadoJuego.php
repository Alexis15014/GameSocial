<?php
/**
 * Modelo: EstadoJuego.php
 * Propósito: Gestionar la relación entre usuarios y videojuegos (biblioteca personal, estados y valoraciones).
 * Proyecto: GameSocial
 */

class EstadoJuego {

    private $conexion;

    // Instanciamos a la conexión de la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

    // Obtenemos la biblioteca completa de un usuario con los detalles de los juegos.
    public function obtenerJuegosUsuario($id_usuario) {
        $sql = "SELECT uv.id_usuario, uv.id_videojuego, uv.estado, uv.valoracion, uv.fecha_estado,
                       v.titulo, v.plataforma, v.tipo, v.imagen_portada
                FROM usuario_videojuego uv
                JOIN videojuegos v ON uv.id_videojuego = v.id_videojuego
                WHERE uv.id_usuario = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Obtenemos la información de seguimiento de un juego específico para un usuario.
    public function obtenerEstadoUsuario($id_usuario, $id_videojuego) {
        $sql = "SELECT estado, valoracion, fecha_estado
                FROM usuario_videojuego
                WHERE id_usuario = :id_usuario AND id_videojuego = :id_videojuego
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizamos o insertamos el estado y valoración de un juego para un usuario.
    public function setEstado($id_usuario, $id_videojuego, $estado, $valoracion) {
        $id_usuario = (int)$id_usuario;
        $id_videojuego = (int)$id_videojuego;

        // --- VALIDACIÓN DE ESTADO ---
        $valores_validos = ['sin_iniciar', 'inacabado', 'terminado', 'completado', 'en_curso', 'abandonado'];
        $estado = strtolower(trim($estado));
        if (!in_array($estado, $valores_validos)) {
            $estado = 'sin_iniciar';
        }

        // --- VALIDACIÓN DE VALORACIÓN ---
        $valoracion = ($valoracion !== null && $valoracion !== '') ? (int)$valoracion : null;
        if ($valoracion !== null && ($valoracion < 1 || $valoracion > 5)) {
            $valoracion = null;
        }

        // --- LÓGICA DE ACTUALIZACIÓN O INSERCIÓN ---
        $checkSql = "SELECT 1 FROM usuario_videojuego WHERE id_usuario = :id_usuario AND id_videojuego = :id_videojuego LIMIT 1";
        $checkStmt = $this->conexion->prepare($checkSql);
        $checkStmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $checkStmt->bindValue(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn()) {
            // Si ya existe, actualizamos
            $sql = "UPDATE usuario_videojuego 
                    SET estado = :estado, valoracion = :valoracion, fecha_estado = NOW()
                    WHERE id_usuario = :id_usuario AND id_videojuego = :id_videojuego";
        } else {
            // Si no existe, creamos el registro
            $sql = "INSERT INTO usuario_videojuego (id_usuario, id_videojuego, estado, valoracion, fecha_estado)
                    VALUES (:id_usuario, :id_videojuego, :estado, :valoracion, NOW())";
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->bindValue(":estado", $estado, PDO::PARAM_STR);
        
        // Manejo de nulos para la valoración en BD
        if ($valoracion === null) {
            $stmt->bindValue(":valoracion", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":valoracion", $valoracion, PDO::PARAM_INT);
        }

        $stmt->execute();
        
        // Registro de actividad en el log del servidor
        error_log("GameSocial Log - Estado: $estado, Valoración: " . ($valoracion ?? 'NULL') . " | User: $id_usuario | Game: $id_videojuego");
    }

    // Calculamos el promedio de valoraciones de la comunidad para un juego específico.
    public function obtenerMediaValoracion($id_videojuego) {
        $sql = "SELECT AVG(valoracion) as media 
                FROM usuario_videojuego 
                WHERE id_videojuego = :id_videojuego AND valoracion IS NOT NULL";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_videojuego", $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['media'] ? (float)$row['media'] : 0.0;
    }

    // Contamos cuántos juegos ha terminado un usuario (estado 'terminado').
    public function contarJuegosFinalizados($id_usuario) {
        $sql = "SELECT COUNT(*) FROM usuario_videojuego 
                WHERE id_usuario = :id_usuario AND estado = 'terminado'";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }

    // Devuelve un array con el conteo de juegos por cada estado para un usuario.
    public function estadisticasPorEstado($id_usuario) {
        $sql = "SELECT estado, COUNT(*) as total
                FROM usuario_videojuego
                WHERE id_usuario = :id_usuario
                GROUP BY estado";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $todos = [
            'sin_iniciar' => 0,
            'inacabado'   => 0,
            'terminado'   => 0,
            'completado'  => 0,
            'en_curso'    => 0,
            'abandonado'  => 0,
        ];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($todos[$row['estado']])) {
                $todos[$row['estado']] = (int)$row['total'];
            }
        }

        return $todos;
    }
}
