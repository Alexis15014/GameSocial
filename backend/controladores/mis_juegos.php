<?php
/**
 * Controlador: mis_juegos.php
 * Propósito: Gestionar y mostrar la colección de juegos personal del usuario.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/EstadoJuego.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';
require_once __DIR__ . '/../helpers/slug.php';

$id_usuario = requiereLogin($conexion);

$modelo_estado = new EstadoJuego($conexion);
$juegos        = $modelo_estado->obtenerJuegosUsuario($id_usuario) ?: [];

// Añadimos la URL de imagen a cada juego
// Usamos la clave 'imagen_url' para mantener compatibilidad con la vista
foreach ($juegos as &$juego) {
    $juego['imagen_url'] = resolverPortada($juego['imagen_portada'] ?? null);
}
unset($juego);

$titulo_pagina = "Mi Colección | GameSocial";

require __DIR__ . '/../../frontend/vistas/mis_juegos.php';
