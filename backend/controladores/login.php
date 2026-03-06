<?php
/**
 * Controlador: login.php
 * Propósito: Gestionar la autenticación de usuarios, sesiones y persistencia (cookies).
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';

$error = null;
$modeloUsuario = new Usuario($conexion);

// --- 1. LÓGICA DE AUTOLOGIN (COOKIE "RECORDARME") ---
// Si no hay sesión pero sí existe la cookie, intentamos loguear automáticamente.
if (!isset($_SESSION['id_usuario']) && isset($_COOKIE['gamesocial_remember'])) {
    $parts = explode('|', $_COOKIE['gamesocial_remember'], 2);
    
    if (count($parts) === 2) {
        list($id_usuario, $token_cookie) = $parts;
        $usuario = $modeloUsuario->obtenerPorId($id_usuario);

        // Verificación segura del token usando hash_equals para evitar ataques de tiempo
        if ($usuario && !empty($usuario['token_recordarme']) && hash_equals($usuario['token_recordarme'], $token_cookie)) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre_usuario'];
            $_SESSION['rol']        = $usuario['rol'];

            header("Location: /gamesocial/backend/controladores/feed.php");
            exit;
        } else {
            // Si el token es inválido, destruimos la cookie por seguridad
            setcookie('gamesocial_remember', '', time() - 3600, '/', '', false, true);
        }
    }
}

// --- 2. PROCESAR EL FORMULARIO DE LOGIN (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $recordarme = isset($_POST['recordarme']); // Verifica si el checkbox está marcado

    // Intentar autenticación mediante el modelo
    $usuario = $modeloUsuario->login($email, $password);

    if ($usuario) {
        // Registro de datos en la sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre']     = $usuario['nombre_usuario'];
        $_SESSION['rol']        = $usuario['rol'];

        // --- GESTIÓN DE COOKIE "RECORDARME" ---
        if ($recordarme) {
            // Generamos un token aleatorio seguro de 32 bytes (64 caracteres hexadecimales)
            $token = bin2hex(random_bytes(32));

            // Guardamos el token en la base de datos
            $modeloUsuario->guardarTokenRecordarme($usuario['id_usuario'], $token);

            // Creamos la cookie: ID_USUARIO | TOKEN
            $cookieValue = $usuario['id_usuario'] . '|' . $token;
            $expires     = time() + (30 * 24 * 60 * 60); // Caducidad en 30 días

            /**
             * Parámetros setcookie:
             * name, value, expire, path, domain, secure, httponly
             * httponly = true (evita que JavaScript acceda a la cookie)
             */
            setcookie('gamesocial_remember', $cookieValue, $expires, '/', '', false, true);
        }

        // Redirección al área principal (Feed)
        header("Location: /gamesocial/backend/controladores/feed.php");
        exit;
        
    } else {
        $error = "El correo electrónico o la contraseña son incorrectos.";
    }
}

// Configuración de metadatos de la página
$titulo_pagina = "Iniciar Sesión | GameSocial";

// Carga de la interfaz de usuario
require_once __DIR__ . '/../../frontend/vistas/login.php';