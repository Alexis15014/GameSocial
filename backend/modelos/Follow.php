<?php
/**
 * Modelo: Follow.php
 * Propósito: Gestionar el sistema de amistades/seguidores entre usuarios.
 * Proyecto: GameSocial
 */

class Follow {

    private $conexion;

	// Instanciamos a la conexión de la base de datos
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Registramos una nueva relación de seguimiento.
    public function seguir($id_seguidor, $id_seguido) {
        $sql = "INSERT IGNORE INTO seguidores (id_seguidor, id_seguido)
                VALUES (:seguidor, :seguido)";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':seguidor' => $id_seguidor,
            ':seguido'  => $id_seguido
        ]);
    }

    // Eliminamos una relación de seguimiento existente.
    public function dejarSeguir($id_seguidor, $id_seguido) {
        $sql = "DELETE FROM seguidores
                WHERE id_seguidor = :seguidor AND id_seguido = :seguido";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':seguidor' => $id_seguidor,
            ':seguido'  => $id_seguido
        ]);
    }

    // Verificamos si existe una relación de seguimiento activa entre dos usuarios.
    public function estaSiguiendo($id_seguidor, $id_seguido) {
        $sql = "SELECT 1 FROM seguidores
                WHERE id_seguidor = :seguidor AND id_seguido = :seguido
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':seguidor' => $id_seguidor,
            ':seguido'  => $id_seguido
        ]);
        
        return $stmt->fetchColumn() !== false;
    }

    // Contamos cuántas personas siguen a un usuario específico.
    public function contarSeguidores($id_usuario) {
        $sql = "SELECT COUNT(*) FROM seguidores WHERE id_seguido = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        
        return (int)$stmt->fetchColumn();
    }

    // Contamos  a cuántas personas está siguiendo un usuario específico.
    public function contarSeguidos($id_usuario) {
        $sql = "SELECT COUNT(*) FROM seguidores WHERE id_seguidor = :id_usuario";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        
        return (int)$stmt->fetchColumn();
    }
}