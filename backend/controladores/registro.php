<?php
/**
 * Controlador: registro.php
 * Propósito: Procesar el formulario de registro, validar duplicados y dar de alta usuarios.
 * Proyecto: GameSocial
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';

$error   = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre           = trim($_POST['nombre']           ?? '');
    $email            = trim($_POST['email']            ?? '');
    $password         = $_POST['password']              ?? '';
    $confirm_password = $_POST['confirm_password']      ?? '';

    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico introducido no tiene un formato válido.";

    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden. Por favor, verifícalas.";

    } elseif (strlen($password) < 6) {
        $error = "La contraseña es demasiado corta (mínimo 6 caracteres).";

    } else {
        $modelo_usuario = new Usuario($conexion);

        // Comprobamos duplicados antes de insertar para evitar errores de restricción en BD
        if ($modelo_usuario->existeEmail($email)) {
            $error = "Este correo electrónico ya está registrado.";

        } elseif ($modelo_usuario->existeNombreUsuario($nombre)) {
            $error = "El nombre de usuario '$nombre' ya está en uso. Elige otro.";

        } elseif ($modelo_usuario->registrar($nombre, $email, $password)) {
            $success = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
            // Limpiamos los campos para que el formulario quede vacío tras el éxito
            $nombre = $email = '';

        } else {
            $error = "Lo sentimos, hubo un error interno al crear la cuenta.";
        }
    }
}

$titulo_pagina = "Únete a GameSocial - Crear Cuenta";

require __DIR__ . '/../../frontend/vistas/registro.php';
