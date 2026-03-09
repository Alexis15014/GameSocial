<?php
/**
 * Helper: auth.php
 * Propósito: Centralizar la verificación de sesión, autorización de roles y el autologin por cookie.
 * Proyecto: GameSocial
 */

// Iniciamos la sesión de forma segura, sin duplicar si ya está activa
function iniciarSesionSegura(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Intentamos restaurar la sesión desde la cookie "Recordarme" si no hay sesión activa.
// Recibimos $conexion como parámetro para evitar problemas de scope con require_once.
function intentarAutologin(?PDO $conexion = null): bool {
    // Si ya hay sesión activa no hace falta hacer nada
    if (!empty($_SESSION['id_usuario'])) {
        return true;
    }

    if (!isset($_COOKIE['gamesocial_remember'])) {
        return false;
    }

    $partes_cookie = explode('|', $_COOKIE['gamesocial_remember'], 2);
    if (count($partes_cookie) !== 2) {
        _eliminarCookieRecordarme();
        return false;
    }

    [$id_cookie, $token_cookie] = $partes_cookie;

    // Si no nos pasaron conexión la cargamos aquí (caso: login.php llama directo a esta función)
    if ($conexion === null) {
        require_once __DIR__ . '/../config/conexion.php';
    }

    require_once __DIR__ . '/../modelos/Usuario.php';

    $modelo_usuario = new Usuario($conexion);
    $usuario_cookie = $modelo_usuario->obtenerPorId((int)$id_cookie);

    // hash_equals evita ataques de temporización al comparar los tokens
    if ($usuario_cookie
        && !empty($usuario_cookie['token_recordarme'])
        && hash_equals($usuario_cookie['token_recordarme'], $token_cookie))
    {
        $_SESSION['id_usuario'] = $usuario_cookie['id_usuario'];
        $_SESSION['nombre']     = $usuario_cookie['nombre_usuario'];
        $_SESSION['rol']        = $usuario_cookie['rol'];
        return true;
    }

    // Token inválido o expirado: borramos la cookie para no reintentar
    _eliminarCookieRecordarme();
    return false;
}

// Eliminamos la cookie "Recordarme" del navegador del cliente
function _eliminarCookieRecordarme(): void {
    $es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('gamesocial_remember', '', time() - 3600, '/', '', $es_https, true);
}

// Requerimos sesión activa. Intenta autologin por cookie antes de redirigir al login.
function requiereLogin(?PDO $conexion = null): int {
    iniciarSesionSegura();
    intentarAutologin($conexion);

    if (empty($_SESSION['id_usuario'])) {
        header("Location: /gamesocial/backend/controladores/login.php");
        exit;
    }

    return (int)$_SESSION['id_usuario'];
}

// Requerimos rol de administrador. Intenta autologin por cookie antes de redirigir al login.
function requiereAdmin(?PDO $conexion = null): int {
    iniciarSesionSegura();
    intentarAutologin($conexion);

    if (empty($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
        header("Location: /gamesocial/backend/controladores/login.php");
        exit;
    }

    return (int)$_SESSION['id_usuario'];
}

// Devolvemos el ID del usuario en sesión sin forzar autenticación.
// Útil en páginas mixtas que funcionan con o sin sesión (ej: perfil público).
function obtenerIdSesion(?PDO $conexion = null): ?int {
    iniciarSesionSegura();
    intentarAutologin($conexion);
    return isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
}
