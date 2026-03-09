<?php
/**
 * Controlador: notificaciones.php
 * Propósito: Gestionar acciones sobre notificaciones (marcar leída / eliminar).
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../helpers/auth.php';

$id_usuario = requiereLogin($conexion);

$modelo_notificacion = new Notificacion($conexion);

// URL a la que volvemos tras procesar la acción
$url_redireccion = $_GET['redirect'] ?? '/gamesocial/backend/controladores/feed.php';

// --- ACCIÓN: Marcar como leída ---

if (isset($_GET['marcar_leida']) && is_numeric($_GET['marcar_leida'])) {
    $id_notificacion = (int)$_GET['marcar_leida'];

    // Verificamos que la notificación pertenezca al usuario en sesión antes de marcarla
    $notificacion = $modelo_notificacion->obtenerPorId($id_notificacion, $id_usuario);

    if ($notificacion) {
        $modelo_notificacion->marcarComoLeida($id_notificacion, $id_usuario);

        // Si tiene videojuego vinculado, redirigimos a su página de detalle
        $id_videojuego_vinculado = $notificacion['id_videojuego'] ?? null;
        if ($id_videojuego_vinculado && $id_videojuego_vinculado > 0) {
            header("Location: /gamesocial/backend/controladores/detalle.php?id=$id_videojuego_vinculado");
            exit;
        }
    }
}

// --- ACCIÓN: Eliminar ---

if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_a_eliminar = (int)$_GET['eliminar'];
    $modelo_notificacion->eliminar($id_a_eliminar, $id_usuario);
}

header("Location: " . $url_redireccion);
exit;
