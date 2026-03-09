<?php
/**
 * Controlador: follow.php
 * Propósito: Gestionar el sistema de seguimiento entre usuarios (Follow/Unfollow).
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Follow.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../helpers/auth.php';

$id_usuario = requiereLogin($conexion);

$id_objetivo = isset($_GET['id']) ? (int)$_GET['id'] : null;

// No permitimos que un usuario se siga a sí mismo
if (!$id_objetivo || $id_objetivo === $id_usuario) {
    $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/gamesocial/frontend/vistas/perfil.php';
    header("Location: " . $url_retorno);
    exit;
}

$modelo_follow       = new Follow($conexion);
$modelo_notificacion = new Notificacion($conexion);

if ($modelo_follow->estaSiguiendo($id_usuario, $id_objetivo)) {
    // Unfollow: dejamos de seguir sin notificación
    $modelo_follow->dejarSeguir($id_usuario, $id_objetivo);
} else {
    // Follow: empezamos a seguir y notificamos al usuario objetivo
    $modelo_follow->seguir($id_usuario, $id_objetivo);

    $nombre_seguidor = $_SESSION['nombre_usuario'] ?? 'Un usuario';
    $modelo_notificacion->crear(
        $id_objetivo,
        'follow',
        "{$nombre_seguidor} ha empezado a seguirte.",
        null
    );
}

$url_redireccion = $_SERVER['HTTP_REFERER'] ?? "/gamesocial/frontend/vistas/perfil.php?id=$id_objetivo";
header("Location: " . $url_redireccion);
exit;
