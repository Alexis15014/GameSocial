<?php
/**
 * Controlador: perfil_publico.php
 * Propósito: Mostrar el perfil público de otro usuario con sus logros y datos de comunidad.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Follow.php';
require_once __DIR__ . '/../modelos/Logro.php';
require_once __DIR__ . '/../helpers/auth.php';

// No forzamos login: el perfil público es accesible sin sesión
iniciarSesionSegura();
$id_sesion = obtenerIdSesion($conexion);

$id_perfil = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_perfil) {
    die("ID de usuario no proporcionado.");
}

// Si el usuario intenta ver su propio perfil público lo mandamos al privado
if ($id_sesion && $id_sesion === $id_perfil) {
    header("Location: perfil.php");
    exit;
}

$modelo_usuario = new Usuario($conexion);
$modelo_follow  = new Follow($conexion);
$modelo_logro   = new Logro($conexion);

$usuario = $modelo_usuario->obtenerPorId($id_perfil);

if (!$usuario) {
    die("El usuario que buscas no existe en nuestra base de datos.");
}

$logros         = $modelo_logro->obtenerLogrosUsuario($id_perfil) ?: [];
$seguidores     = $modelo_follow->contarSeguidores($id_perfil);
$seguidos       = $modelo_follow->contarSeguidos($id_perfil);
$esta_siguiendo = $id_sesion ? $modelo_follow->estaSiguiendo($id_sesion, $id_perfil) : false;

$nombre_usuario_perfil = htmlspecialchars($usuario['nombre_usuario'] ?? 'Gamer');
$titulo_pagina         = "Perfil de " . $nombre_usuario_perfil . " | GameSocial";

require_once __DIR__ . '/../../frontend/vistas/perfil_publico.php';
