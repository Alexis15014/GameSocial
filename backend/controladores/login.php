<?php
/**
 * Controlador: login.php
 * Propósito: Gestionar la autenticación de usuarios y la cookie "Recordarme".
 * Proyecto: GameSocial
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../helpers/auth.php';

// Duración de la cookie "Recordarme": 30 días en segundos
const DURACION_COOKIE_RECORDARME = 30 * 24 * 60 * 60;

// Si ya hay sesión activa o la cookie es válida, mandamos directo al feed
if (intentarAutologin()) {
    header("Location: /gamesocial/inicio");
    exit;
}

$error          = null;
$modelo_usuario = new Usuario($conexion);

// --- PROCESAR FORMULARIO DE LOGIN (POST) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email']    ?? '');
    $password   = trim($_POST['password'] ?? '');
    $recordarme = isset($_POST['recordarme']);

    $usuario = $modelo_usuario->login($email, $password);

    if ($usuario) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre']     = $usuario['nombre_usuario'];
        $_SESSION['rol']        = $usuario['rol'];

        if ($recordarme) {
            // Generamos un token de 64 caracteres hexadecimales (256 bits de entropía)
            $token_generado = bin2hex(random_bytes(32));
            $modelo_usuario->guardarTokenRecordarme($usuario['id_usuario'], $token_generado);

            // secure dinámico: true en HTTPS, false en local
            $es_https     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $valor_cookie = $usuario['id_usuario'] . '|' . $token_generado;
            $expiracion   = time() + DURACION_COOKIE_RECORDARME;

            // httponly=true evita que JavaScript lea la cookie (protección XSS)
            setcookie('gamesocial_remember', $valor_cookie, $expiracion, '/', '', $es_https, true);
        }

        header("Location: /gamesocial/inicio");
        exit;
    }

    $error = "El correo electrónico o la contraseña son incorrectos.";
}

$titulo_pagina = "Iniciar Sesión | GameSocial";

require __DIR__ . '/../../frontend/vistas/login.php';
