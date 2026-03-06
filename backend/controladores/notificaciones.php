<?php
/**
 * Controlador: notificaciones.php
 * Propósito: Gestionar acciones de notificaciones (leída/eliminar) y redirecciones.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Notificacion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$modelo_notificacion = new Notificacion($conexion);

// Si no hay sesión, protegemos el acceso
if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/feed.php");
    exit;
}

// Capturamos la página a la que debemos volver
$url_redireccion = $_GET['redirect'] ?? '/gamesocial/backend/controladores/feed.php';

// --- ACCIÓN: MARCAR COMO LEÍDA ---
if (isset($_GET['marcar_leida']) && is_numeric($_GET['marcar_leida'])) {
    $id_notificacion = (int)$_GET['marcar_leida'];
    $notificacion = $modelo_notificacion->obtenerPorId($id_notificacion, $id_usuario);
    
    if ($notificacion) {
        $modelo_notificacion->marcarComoLeida($id_notificacion, $id_usuario);
        $videojuego_id = $notificacion['id_videojuego'] ?? null;

        // Si tiene videojuego, redirigimos al detalle, si no, a la página de origen
        if ($videojuego_id && $videojuego_id > 0) {
            header("Location: /gamesocial/backend/controladores/detalle.php?id=$videojuego_id");
            exit;
        }
    }
}

// --- ACCIÓN: ELIMINAR ---
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $modelo_notificacion->eliminar((int)$_GET['eliminar'], $id_usuario);
}

// Redirección final por defecto
header("Location: " . $url_redireccion);
exit;