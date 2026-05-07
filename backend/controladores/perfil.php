<?php
/**
 * Controlador: perfil.php
 * Propósito: Gestionar la actualización de avatar y biografía del usuario en sesión.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../modelos/Logro.php';
require_once __DIR__ . '/../modelos/EstadoJuego.php';
require_once __DIR__ . '/../modelos/Lista.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';
require_once __DIR__ . '/../helpers/slug.php';

$id_usuario = requiereLogin($conexion);

$modelo_usuario      = new Usuario($conexion);
$modelo_notificacion = new Notificacion($conexion);
$modelo_logro        = new Logro($conexion);
$modelo_estado       = new EstadoJuego($conexion);
$modelo_lista        = new Lista($conexion);

// --- ACCIÓN: Subida de avatar ---
if (!empty($_FILES['avatar']['name'])) {
    $archivo_subido    = $_FILES['avatar'];
    $ruta_tmp          = $archivo_subido['tmp_name'];
    $extension_archivo = strtolower(pathinfo($archivo_subido['name'], PATHINFO_EXTENSION));

    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension_archivo, $extensiones_permitidas)) {
        $directorio_destino = __DIR__ . '/../../frontend/assets/avatars/';

        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        $nombre_archivo_nuevo = 'usuario_' . $id_usuario . '_' . time() . '.' . $extension_archivo;
        $ruta_fisica_destino  = $directorio_destino . $nombre_archivo_nuevo;
        $ruta_relativa_bd     = 'frontend/assets/avatars/' . $nombre_archivo_nuevo;

        if (move_uploaded_file($ruta_tmp, $ruta_fisica_destino)) {
            $modelo_usuario->actualizarFotoPerfil($id_usuario, $ruta_relativa_bd);
            $modelo_notificacion->crear($id_usuario, 'perfil', '¡Tu nuevo avatar luce genial!');

            header("Location: /gamesocial/perfil?success=avatar");
            exit;
        }
    }
}

// --- ACCIÓN: Actualización de biografía ---
if (isset($_POST['biografia'])) {
    $nueva_biografia = trim($_POST['biografia']);

    if ($modelo_usuario->actualizarBiografia($id_usuario, $nueva_biografia)) {
        $modelo_notificacion->crear($id_usuario, 'perfil', 'Has actualizado tu descripción de perfil.');

        header("Location: /gamesocial/perfil?success=bio");
        exit;
    }
}

// --- CARGA DE DATOS PARA LA VISTA ---
$usuario       = $modelo_usuario->obtenerPorId($id_usuario);
$logros        = $modelo_logro->obtenerLogrosUsuario($id_usuario) ?: [];
$stats_estados = $modelo_estado->estadisticasPorEstado($id_usuario);

// Listas del usuario (todas, no solo públicas)
$listas_perfil = $modelo_lista->obtenerListasUsuario($id_usuario);
foreach ($listas_perfil as &$l) {
    $l['portada_url'] = resolverPortada($l['portada_lista'] ?? null);
}
unset($l);

$nombre_perfil = $usuario['nombre_usuario'] ?? 'Gamer';
$titulo_pagina = "Perfil de " . $nombre_perfil . " | GameSocial";

require __DIR__ . '/../../frontend/vistas/perfil.php';
