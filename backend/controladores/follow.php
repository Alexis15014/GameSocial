<?php
/**
 * Controlador: follow.php
 * Propósito: Gestionar el sistema de Seguimiento entre usuarios (Follow/Unfollow).
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Follow.php';
require_once __DIR__ . '/../modelos/Notificacion.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Validación de Sesión
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// 2. Captura del ID Objetivo (el usuario al que se quiere seguir)
$id_objetivo = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Seguridad: No seguirse a uno mismo ni procesar IDs nulos
if (!$id_objetivo || $id_objetivo === $id_usuario) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/gamesocial/frontend/vistas/perfil.php';
    header("Location: " . $referer);
    exit;
}

$modelo_follow = new Follow($conexion);
$modelo_notificacion = new Notificacion($conexion);

// 3. Lógica de Seguimiento (Toggle)
if ($modelo_follow->estaSiguiendo($id_usuario, $id_objetivo)) {
    // Acción: Unfollow
    $modelo_follow->dejarSeguir($id_usuario, $id_objetivo);
} else {
    // Acción: Follow
    $modelo_follow->seguir($id_usuario, $id_objetivo);

    // 4. Notificación: Solo cuando se empieza a seguir (UX limpia)
    // Obtenemos el nombre del seguidor para que la notificación sea personal
    $nombre_seguidor = $_SESSION['nombre_usuario'] ?? 'Un usuario';
    
    $modelo_notificacion->crear(
        $id_objetivo, // El dueño del perfil recibe la notificación
        'follow',
        "{$nombre_seguidor} ha empezado a seguirte.",
        null // No hay ID de videojuego asociado aquí
    );
}

// 5. Redirección de Retorno
$redireccion = $_SERVER['HTTP_REFERER'] ?? "/gamesocial/frontend/vistas/perfil.php?id=$id_objetivo";
header("Location: " . $redireccion);
exit;