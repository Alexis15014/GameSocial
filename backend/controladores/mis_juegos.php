<?php
/**
 * Controlador: mis_juegos.php
 * Propósito: Gestionar y mostrar la colección de juegos personal del usuario.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/EstadoJuego.php';

// --- 1. VALIDACIÓN DE SESIÓN ---
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// --- 2. OBTENCIÓN DE DATOS ---
$modelo_estado = new EstadoJuego($conexion);
$juegos = $modelo_estado->obtenerJuegosUsuario($id_usuario) ?: [];

// --- 3. PROCESAMIENTO DE IMÁGENES ---
foreach ($juegos as &$juego) {
    // Verificamos si la imagen existe físicamente
    if (!empty($juego['imagen_portada']) && file_exists(__DIR__ . '/../../' . $juego['imagen_portada'])) {
        $juego['imagen_url'] = '../../' . $juego['imagen_portada'];
    } else {
        // Placeholder por defecto si no hay imagen
        $juego['imagen_url'] = '../../frontend/assets/img/placeholder_juego.png';
    }
}
unset($juego); // Limpiar referencia del loop

$titulo_pagina = "Mi Colección | GameSocial";

// --- 4. CARGA DE VISTA ---
require_once __DIR__ . '/../../frontend/vistas/mis_juegos.php';