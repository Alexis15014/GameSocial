<?php
/**
 * Controlador: perfil_publico.php
 * Propósito: Mostrar el perfil público de otro usuario con logros, listas y estadísticas.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Follow.php';
require_once __DIR__ . '/../modelos/Logro.php';
require_once __DIR__ . '/../modelos/Lista.php';
require_once __DIR__ . '/../modelos/EstadoJuego.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';

iniciarSesionSegura();
$id_sesion = obtenerIdSesion($conexion);

$id_perfil = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_perfil) {
    die("ID de usuario no proporcionado.");
}

if ($id_sesion && $id_sesion === $id_perfil) {
    header("Location: perfil.php");
    exit;
}

$modelo_usuario = new Usuario($conexion);
$modelo_follow  = new Follow($conexion);
$modelo_logro   = new Logro($conexion);
$modelo_lista   = new Lista($conexion);
$modelo_estado  = new EstadoJuego($conexion);

$usuario = $modelo_usuario->obtenerPorId($id_perfil);

if (!$usuario) {
    die("El usuario que buscas no existe en nuestra base de datos.");
}

$logros         = $modelo_logro->obtenerLogrosUsuario($id_perfil) ?: [];
$seguidores     = $modelo_follow->contarSeguidores($id_perfil);
$seguidos       = $modelo_follow->contarSeguidos($id_perfil);
$esta_siguiendo = $id_sesion ? $modelo_follow->estaSiguiendo($id_sesion, $id_perfil) : false;

// Listas públicas
$listas_publicas = $modelo_lista->obtenerListasPublicasUsuario($id_perfil);
foreach ($listas_publicas as &$l) {
    $l['portada_url'] = resolverPortada($l['portada_lista'] ?? null);
}
unset($l);

// Estadísticas de colección
$stats_estados = $modelo_estado->estadisticasPorEstado($id_perfil);

$nombre_usuario_perfil = htmlspecialchars($usuario['nombre_usuario'] ?? 'Gamer');
$titulo_pagina         = "Perfil de " . $nombre_usuario_perfil . " | GameSocial";

require_once __DIR__ . '/../../frontend/vistas/perfil_publico.php';
