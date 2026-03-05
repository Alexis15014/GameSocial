<?php
/**
 * Controlador: like.php
 * Propósito: Gestionar el sistema de Toggle Like (Poner/Quitar) para videojuegos y comentarios.
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Like.php';
require_once __DIR__ . '/../modelos/Notificacion.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Verificación de Seguridad
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// 2. Captura y Validación de Datos
$tipo        = $_GET['tipo'] ?? null;
$id_objetivo = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$tipo || !$id_objetivo) {
    die("Error: Parámetros insuficientes para procesar el Like.");
}

$modelo_like         = new Like($conexion);
$modelo_notificacion = new Notificacion($conexion);

// 3. Ejecución de la lógica (Toggle)
// $resultado será true si se añadió el like, false si se eliminó.
$resultado = $modelo_like->toggle($id_usuario, $tipo, $id_objetivo);

// 4. Gestión de Notificaciones (Solo si es un nuevo Like)
if ($resultado === true) {
    // Determinamos si el like es sobre un juego para vincularlo en la notificación
    $id_referencia = ($tipo === 'videojuego') ? $id_objetivo : null;
    
    // Personalizamos el mensaje según el tipo para que sea más natural
    $mensaje = ($tipo === 'videojuego') 
        ? "Te ha gustado un videojuego." 
        : "Has dado like a un comentario.";

    $modelo_notificacion->crear(
        $id_usuario,
        'like',
        $mensaje,
        $id_referencia
    );
}

// 5. Redirección Inteligente
// Volvemos a la página anterior, o al índice si no hay referer.
$redireccion = $_SERVER['HTTP_REFERER'] ?? '/gamesocial/frontend/vistas/index.php';
header("Location: " . $redireccion);
exit;