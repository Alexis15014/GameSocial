<?php
/**
 * Modelo: Logro.php
 * Propósito: Gestionar el desbloqueo de logros y las recompensas del usuario.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/Notificacion.php';

class Logro {

    private $conexion;

    // Instanciamos a la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

	// Obtenemos la lista de logros que un usuario ya ha desbloqueado.
    public function obtenerLogrosUsuario($id_usuario) {
        $sql = "SELECT l.nombre, l.descripcion, ul.fecha_obtencion
                FROM usuario_logro ul
                JOIN logros l ON ul.id_logro = l.id_logro
                WHERE ul.id_usuario = :id_usuario
                ORDER BY ul.fecha_obtencion DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Asignamos un logro a un usuario si no lo tiene ya.
    public function asignarLogro($id_usuario, $id_logro) {

        // 1. Verificar si el usuario ya posee el logro
        $sql_check = "SELECT 1 FROM usuario_logro 
                      WHERE id_usuario = :id_usuario AND id_logro = :id_logro 
                      LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql_check);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(":id_logro", $id_logro, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {

            // 2. Insertar el nuevo logro obtenido
            $sql_insert = "INSERT INTO usuario_logro (id_usuario, id_logro, fecha_obtencion)
                           VALUES (:id_usuario, :id_logro, NOW())";
            
            $stmt_insert = $this->conexion->prepare($sql_insert);
            $stmt_insert->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmt_insert->bindValue(":id_logro", $id_logro, PDO::PARAM_INT);
            $stmt_insert->execute();

            // 3. Crear notificación automática
            $this->notificarLogro($id_usuario, $id_logro);
        }
    }

    // Método interno para gestionar la notificación de un logro recién obtenido.
    private function notificarLogro($id_usuario, $id_logro) {
        $modelo_notificacion = new Notificacion($this->conexion);

        // Obtener el nombre del logro para el mensaje
        $sql = "SELECT nombre FROM logros WHERE id_logro = :id_logro LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_logro", $id_logro, PDO::PARAM_INT);
        $stmt->execute();
        $logro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($logro) {
            $modelo_notificacion->crear(
                $id_usuario,
                'logro',
                "🏆 ¡Felicidades! Has desbloqueado el logro: " . htmlspecialchars($logro['nombre'])
            );
        }
    }

    // Verifica las condiciones de actividad para otorgar logros pendientes personalizados.
    public function comprobarLogros($id_usuario, $modelo_estado, $modelo_comentario) {
		
        // Logro ID 1: "Primer comentario"
        $total_comentarios = $modelo_comentario->contarComentariosUsuario($id_usuario);
        if ($total_comentarios >= 1) {
            $this->asignarLogro($id_usuario, 1);
        }

        // Logro ID 2: "Veterano" (Haber finalizado 5 juegos)
        $juegos_finalizados = $modelo_estado->contarJuegosFinalizados($id_usuario);
        if ($juegos_finalizados >= 5) {
            $this->asignarLogro($id_usuario, 2);
        }
    }
}