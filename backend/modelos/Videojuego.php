<?php

/**
 * Modelo: Videojuego.php
 * Propósito: Gestionar el catálogo de juegos, detalles y búsquedas.
 * Proyecto: GameSocial
 */

class Videojuego{

	private $conexion;

    // Instanciamos a la base de datos
    public function __construct($conexion_db) {
        $this->conexion = $conexion_db;
    }

    // Obtenemos todos los videojuegos ordenados por fecha de lanzamiento
    public function obtenerTodos() {
        $sql = "SELECT 
                    id_videojuego,
                    titulo,
                    fecha_lanzamiento,
                    genero,
                    desarrolladora,
                    plataforma,
                    imagen_portada
                FROM videojuegos
                ORDER BY fecha_lanzamiento DESC";
    
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
  
	// Obtenemos la ficha técnica completa de un videojuego por su ID.
    public function obtenerPorId($id) {
        $sql = "SELECT 
                    id_videojuego, 
                    titulo, 
                    descripcion, 
                    genero, 
                    fecha_lanzamiento, 
                    desarrolladora, 
                    plataforma, 
                    imagen_portada
                FROM videojuegos
                WHERE id_videojuego = :id
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Buscamos videojuegos por coincidencia en el título.
    public function buscarPorTitulo($termino) {
        $sql = "SELECT 
                    id_videojuego, 
                    titulo, 
                    genero, 
                    plataforma, 
                    imagen_portada
                FROM videojuegos
                WHERE titulo LIKE :termino
                ORDER BY titulo ASC
                LIMIT 30"; // Limitamos para optimizar rendimiento
        
        $stmt = $this->conexion->prepare($sql);
        $likeTermino = '%' . $termino . '%';
        $stmt->bindValue(':termino', $likeTermino, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}


?>