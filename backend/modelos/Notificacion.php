<?php

/**
 * Modelo: Notificacion.php
 * Propósito: Gestionar la persistencia de las notificaciones de los usuarios.
 * Proyecto: GameSocial
 */

class Notificacion {

    private $conexion;

	// Instanciamos a la base de datos
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Insertamos la notificación en la base de datos
    public function crear($id_usuario, $tipo, $mensaje, $id_videojuego = null) {
        $sql = "INSERT INTO notificaciones (id_usuario, tipo, mensaje, id_videojuego)
                VALUES (:id_usuario, :tipo, :mensaje, :id_videojuego)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_usuario'    => $id_usuario,
            ':tipo'          => $tipo,
            ':mensaje'       => $mensaje,
            ':id_videojuego' => $id_videojuego
        ]);
    }

    // Recuperamos el historial de notificaciones de un usuario ordenadas por fecha.
    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT *
                FROM notificaciones
                WHERE id_usuario = :id_usuario
                ORDER BY fecha_notificacion DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcamos todas las notificaciones de un usuario como leídas.
    public function marcarTodasComoLeidas($id_usuario) {
        $sql = "UPDATE notificaciones
                SET leida = 1
                WHERE id_usuario = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
    }

    // Marcamos una notificación específica como leída, validando su propietario.
    public function marcarComoLeida($id_notificacion, $id_usuario) {
        $sql = "UPDATE notificaciones
                SET leida = 1
                WHERE id_notificacion = :id_notificacion
                AND id_usuario = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_notificacion' => $id_notificacion,
            ':id_usuario'      => $id_usuario
        ]);
    }

	// Eliminamos permanentemente una notificación específica.
    public function eliminar($id_notificacion, $id_usuario) {
        $sql = "DELETE FROM notificaciones
                WHERE id_notificacion = :id_notificacion
                AND id_usuario = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_notificacion' => $id_notificacion,
            ':id_usuario'      => $id_usuario
        ]);
    }

    // Obtenemos los datos de una sola notificación validando el acceso del usuario.
    public function obtenerPorId($id_notificacion, $id_usuario) {
        $sql = "SELECT *
                FROM notificaciones
                WHERE id_notificacion = :id_notificacion
                AND id_usuario = :id_usuario
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_notificacion' => $id_notificacion,
            ':id_usuario'      => $id_usuario
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}