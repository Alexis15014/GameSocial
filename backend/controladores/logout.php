<?php
/**
 * Controlador: logout.php
 * Propósito: Cerrar la sesión del usuario de forma segura y limpiar la cookie "Recordarme".
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/auth.php';

iniciarSesionSegura();

// Invalidamos el token en BD para que la cookie no pueda usarse en el futuro
if (isset($_SESSION['id_usuario'])) {
    try {
        $stmt_invalidar = $conexion->prepare("UPDATE usuarios SET token_recordarme = NULL WHERE id_usuario = :id");
        $stmt_invalidar->execute([':id' => $_SESSION['id_usuario']]);
    } catch (PDOException $e) {
        // Error silencioso: el logout debe completarse aunque falle la BD
    }
}

// Vaciamos los datos de sesión del servidor
$_SESSION = [];

// Eliminamos también la cookie de sesión del navegador
if (ini_get("session.use_cookies")) {
    $params_cookie_sesion = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params_cookie_sesion["path"],
        $params_cookie_sesion["domain"],
        $params_cookie_sesion["secure"],
        $params_cookie_sesion["httponly"]
    );
}

session_destroy();

// Eliminamos la cookie "Recordarme" del cliente
$es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
setcookie('gamesocial_remember', '', time() - 3600, '/', '', $es_https, true);

header("Location: /gamesocial/backend/controladores/login.php?logout=success");
exit;
