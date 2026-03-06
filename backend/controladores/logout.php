<?php
/**
 * Controlador: logout.php
 * Propósito: Finalizar la sesión del usuario de forma segura y limpiar rastros de autenticación.
 */

require_once __DIR__ . '/../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpieza en Base de Datos (Invalidar "Recordarme")
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET token_recordarme = NULL WHERE id_usuario = :id");
        $stmt->execute([':id' => $id_usuario]);
    } catch (PDOException $e) {
        // Error silencioso para no interrumpir el logout
    }
}

// 2. Destrucción de la Sesión en el Servidor
$_SESSION = []; // Vaciar el array
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}
session_destroy();

// 3. Limpieza de Cookies de Cliente
$isSecure = false; // Cambiar a true si usas HTTPS en el futuro
setcookie('gamesocial_remember', '', time() - 3600, '/', '', $isSecure, true);

// 4. Redirección Final
header("Location: /gamesocial/backend/controladores/login.php?logout=success");
exit;