<?php
/**
 * Controlador: like.php
 * Propósito: Gestionar el toggle de likes para posts, respuestas y videojuegos.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Like.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../helpers/auth.php';

$id_usuario = requiereLogin($conexion);

$tipo        = $_GET['tipo'] ?? null;
$id_objetivo = isset($_GET['id']) ? (int)$_GET['id'] : null;

$tipos_permitidos = ['post', 'respuesta', 'videojuego', 'comentario'];

if (!$tipo || !in_array($tipo, $tipos_permitidos, true) || !$id_objetivo) {
    die("Error: Parámetros insuficientes o tipo de like no válido.");
}

$modelo_like         = new Like($conexion);
$modelo_notificacion = new Notificacion($conexion);

// toggle() devuelve true si se añadió el like, false si se eliminó
$se_dio_like = $modelo_like->toggle($id_usuario, $tipo, $id_objetivo);

// Solo notificamos cuando se da like, nunca cuando se quita
if ($se_dio_like === true) {
    // Vinculamos la notificación al videojuego solo si el like es directamente sobre él
    $id_videojuego_notificacion = ($tipo === 'videojuego') ? $id_objetivo : null;
    $mensaje_notificacion = ($tipo === 'videojuego')
        ? "Te ha gustado un videojuego."
        : "Has dado like a un comentario.";

    $modelo_notificacion->crear($id_usuario, 'like', $mensaje_notificacion, $id_videojuego_notificacion);
}

$url_redireccion = $_SERVER['HTTP_REFERER'] ?? '/gamesocial/frontend/vistas/index.php';
header("Location: " . $url_redireccion);
exit;
