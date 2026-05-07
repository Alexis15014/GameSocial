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
    $error_subida      = $archivo_subido['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($error_subida !== UPLOAD_ERR_OK) {
        $mensajes_error_upload = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo supera el límite permitido por el servidor (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el límite indicado en el formulario.',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió de forma incompleta.',
            UPLOAD_ERR_NO_FILE    => 'No se recibió ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el servidor. Revisa los permisos de la carpeta avatars/.',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP bloqueó la subida.',
        ];
        $motivo = $mensajes_error_upload[$error_subida] ?? 'Error desconocido al subir el archivo.';
        header("Location: /gamesocial/perfil?error_avatar=" . urlencode($motivo));
        exit;
    }

    $ruta_tmp          = $archivo_subido['tmp_name'];
    $extension_archivo = strtolower(pathinfo($archivo_subido['name'], PATHINFO_EXTENSION));

    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension_archivo, $extensiones_permitidas)) {
        $directorio_destino = __DIR__ . '/../../frontend/assets/avatars/';

        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0775, true);
        }

        // Nos aseguramos de que la carpeta tenga permisos de escritura
        if (!is_writable($directorio_destino)) {
            @chmod($directorio_destino, 0775);
        }

        $nombre_archivo_nuevo = 'usuario_' . $id_usuario . '_' . time() . '.' . $extension_archivo;
        $ruta_fisica_destino  = $directorio_destino . $nombre_archivo_nuevo;
        $ruta_relativa_bd     = 'frontend/assets/avatars/' . $nombre_archivo_nuevo;

        if (move_uploaded_file($ruta_tmp, $ruta_fisica_destino)) {
            $modelo_usuario->actualizarFotoPerfil($id_usuario, $ruta_relativa_bd);
            $modelo_notificacion->crear($id_usuario, 'perfil', '¡Tu nuevo avatar luce genial!');

            header("Location: /gamesocial/perfil?success=avatar");
            exit;
        } else {
            $motivo = is_writable($directorio_destino)
                ? 'No se pudo mover el archivo. Comprueba que PHP tiene permisos de escritura en la carpeta avatars/.'
                : 'La carpeta avatars/ no tiene permisos de escritura en el servidor.';
            header("Location: /gamesocial/perfil?error_avatar=" . urlencode($motivo));
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
