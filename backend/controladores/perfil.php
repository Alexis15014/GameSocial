<?php
/**
 * Controlador: perfil.php
 * Propósito: Gestionar la información del usuario, actualización de avatar y biografía.
 * Proyecto: GameSocial
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../modelos/Logro.php';

// --- 1. VERIFICACIÓN DE ACCESO ---
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// Instancia de modelos
$modelo_usuario      = new Usuario($conexion);
$modelo_notificacion = new Notificacion($conexion);
$modelo_logro        = new Logro($conexion);

// --- 2. PROCESAR ACTUALIZACIÓN DE AVATAR (FILES) ---
if (!empty($_FILES['avatar']['name'])) {
    $archivo   = $_FILES['avatar'];
    $nombreTmp = $archivo['tmp_name'];
    $ext       = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $extPermitidas)) {
        // Definir rutas (usamos una ruta relativa para la base de datos)
        $dirDestino = __DIR__ . '/../../frontend/assets/avatars/';
        if (!file_exists($dirDestino)) {
            mkdir($dirDestino, 0777, true);
        }

        $nombreArchivo = 'usuario_' . $id_usuario . '_' . time() . '.' . $ext;
        $rutaFisica    = $dirDestino . $nombreArchivo;
        $rutaRelativa  = 'frontend/assets/avatars/' . $nombreArchivo;

        if (move_uploaded_file($nombreTmp, $rutaFisica)) {
            // Actualizar en base de datos
            $modelo_usuario->actualizarFotoPerfil($id_usuario, $rutaRelativa);
            
            // Notificar al usuario
            $modelo_notificacion->crear($id_usuario, 'perfil', '¡Tu nuevo avatar luce genial!');
            
            // Refrescar para ver cambios
            header("Location: perfil.php?success=avatar");
            exit;
        }
    }
}

// --- 3. PROCESAR ACTUALIZACIÓN DE BIOGRAFÍA (POST) ---
if (isset($_POST['biografia'])) {
    $nuevaBio = trim($_POST['biografia']);
    
    if ($modelo_usuario->actualizarBiografia($id_usuario, $nuevaBio)) {
        $modelo_notificacion->crear($id_usuario, 'perfil', 'Has actualizado tu descripción de perfil.');
        
        header("Location: perfil.php?success=bio");
        exit;
    }
}

// --- 4. CARGA DE DATOS PARA LA VISTA ---
$usuario = $modelo_usuario->obtenerPorId($id_usuario);
$logros  = $modelo_logro->obtenerLogrosUsuario($id_usuario) ?: [];

// Título dinámico
$nombre_perfil = $usuario['nombre_usuario'] ?? 'Gamer';
$titulo_pagina = "Perfil de " . $nombre_perfil . " | GameSocial";

// Carga del Frontend
require_once __DIR__ . '/../../frontend/vistas/perfil.php';