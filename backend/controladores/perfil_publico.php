<?php
/**
 * Controlador: perfil_publico.php
 * Propósito: Mostrar el perfil de otros usuarios, sus logros y gestionar el sistema de seguimiento.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Follow.php';
require_once __DIR__ . '/../modelos/Logro.php';

// --- 1. VALIDACIÓN DE IDENTIDAD ---
$id_sesion = $_SESSION['id_usuario'] ?? null;
$id_perfil = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_perfil) {
    die("ID de usuario no proporcionado.");
}

// Si el usuario intenta ver su propio perfil público, lo mandamos a su perfil privado
if ($id_sesion && $id_sesion === $id_perfil) {
    header("Location: perfil.php");
    exit;
}

// --- 2. INSTANCIA DE MODELOS ---
$modelo_usuario = new Usuario($conexion);
$modelo_follow  = new Follow($conexion);
$modelo_logro   = new Logro($conexion);

// --- 3. OBTENCIÓN DE DATOS ---
$usuario = $modelo_usuario->obtenerPorId($id_perfil);

if (!$usuario) {
    // Podrías redirigir a una página 404 personalizada
    die("El usuario que buscas no existe en nuestra base de datos.");
}

// Datos de comunidad
$logros         = $modelo_logro->obtenerLogrosUsuario($id_perfil) ?: [];
$seguidores     = $modelo_follow->contarSeguidores($id_perfil);
$seguidos       = $modelo_follow->contarSeguidos($id_perfil);
$esta_siguiendo = ($id_sesion) ? $modelo_follow->estaSiguiendo($id_sesion, $id_perfil) : false;

// --- 4. CONFIGURACIÓN DE VISTA ---
$nombre_usuario_perfil = htmlspecialchars($usuario['nombre_usuario'] ?? 'Gamer');
$titulo_pagina = "Perfil de " . $nombre_usuario_perfil . " | GameSocial";

// Cargar el frontend
require_once __DIR__ . '/../../frontend/vistas/perfil_publico.php';