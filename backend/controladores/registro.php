<?php
/**
 * Controlador: register.php
 * Propósito: Procesar el formulario de registro, validar duplicados y dar de alta usuarios.
 * Proyecto: GameSocial
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';

$error = null;
$success = null;

// Solo procesamos si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Captura y limpieza de datos (usamos ?? para evitar errores si el campo no existe)
    $nombre           = trim($_POST['nombre'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- BLOQUE DE VALIDACIONES ---
    
    // 1. Verificar campos vacíos
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";
    } 
    // 2. Validar formato de email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico introducido no tiene un formato válido.";
    } 
    // 3. Validar que las contraseñas coincidan
    elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden. Por favor, verifícalas.";
    } 
    // 4. Validar longitud mínima de contraseña
    elseif (strlen($password) < 6) {
        $error = "La contraseña es demasiado corta (mínimo 6 caracteres).";
    }
    else {
        // Instanciamos el modelo para realizar consultas a la DB
        $modeloUsuario = new Usuario($conexion);

        /**
         * COMPROBACIÓN DE DUPLICADOS
         * Para evitar "Fatal errors" por restricciones UNIQUE en la base de datos,
         * comprobamos manualmente si los datos ya existen.
         */

        // 5. Verificar si el email ya está en uso
        if ($modeloUsuario->existeEmail($email)) {
            $error = "Este correo electrónico ya está registrado.";
        } 
        // 6. Verificar si el nombre de usuario ya está en uso
        // Esto evita el error "Duplicate entry" para la columna 'nombre_usuario'
        elseif ($modeloUsuario->existeNombreUsuario($nombre)) {
            $error = "El nombre de usuario '$nombre' ya está pillado. Elige otro.";
        }
        else {
            // 7. Intentar el registro final si todo está limpio
            if ($modeloUsuario->registrar($nombre, $email, $password)) {
                $success = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
                
                // Opcional: Limpiar variables para que no se muestren en el formulario tras el éxito
                $nombre = $email = '';
            } else {
                $error = "Lo sentimos, hubo un error interno al crear la cuenta.";
            }
        }
    }
}

// Título dinámico para la pestaña del navegador
$titulo_pagina = "Únete a GameSocial - Crear Cuenta";

// Carga de la vista correspondiente (Frontend)
require_once __DIR__ . '/../../frontend/vistas/registro.php';