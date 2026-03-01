<?php
/**
 * Modelo: Like.php
 * Propósito: Gestionar el sistema de reacciones (Likes) en diferentes entidades (comentarios, publicaciones, etc.).
 * Proyecto: GameSocial
 */

class Like {

    private $conexion;

    // Instanciamos a conexión de la base de datos
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Alternamos el estado de un Like. Si el usuario ya dio like, lo elimina; si no, lo crea.
    public function toggle($id_usuario, $tipo, $id_objetivo) {
        try {

            // Si ya existe, lo eliminamos (Quitar Like)
            if ($this->existe($id_usuario, $tipo, $id_objetivo)) {
                $sql = "DELETE FROM likes 
                        WHERE id_usuario = :id_usuario 
                        AND tipo = :tipo 
                        AND id_objetivo = :id_objetivo";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute([
                    ':id_usuario'  => $id_usuario,
                    ':tipo'        => $tipo,
                    ':id_objetivo' => $id_objetivo
                ]);
                return false; 
            }
    
            // Si no existe, lo insertamos (Dar Like)
            $sql = "INSERT INTO likes (id_usuario, tipo, id_objetivo) 
                    VALUES (:id_usuario, :tipo, :id_objetivo)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':id_usuario'  => $id_usuario,
                ':tipo'        => $tipo,
                ':id_objetivo' => $id_objetivo
            ]);
            return true; 
    
        } catch (PDOException $e) {

            // Código 23000: Error de integridad (posible duplicado en concurrencia)
            if ($e->getCode() == 23000) {
                return true; 
            }
            throw $e;
        }
    }

    // Verificamos si un usuario específico ya ha dado like a un objetivo.
    public function existe($id_usuario, $tipo, $id_objetivo) {
        $sql = "SELECT 1 FROM likes 
                WHERE id_usuario = :id_usuario 
                AND tipo = :tipo 
                AND id_objetivo = :id_objetivo 
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_usuario'  => $id_usuario,
            ':tipo'        => $tipo,
                ':id_objetivo' => $id_objetivo
        ]);
        
        return $stmt->fetchColumn() !== false;
    }

    // Contamos el total de likes que tiene un objetivo específico.
    public function contar($tipo, $id_objetivo) {
        $sql = "SELECT COUNT(*) FROM likes 
                WHERE tipo = :tipo 
                AND id_objetivo = :id_objetivo";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':tipo'        => $tipo,
            ':id_objetivo' => $id_objetivo
        ]);
        
        return (int)$stmt->fetchColumn();
    }
}