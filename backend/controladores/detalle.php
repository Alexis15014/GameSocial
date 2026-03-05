<?php
/**
 * Controlador: detalle.php
 * Propósito: Gestionar la lógica de la página de detalle de un videojuego.
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Videojuego.php';
require_once __DIR__ . '/../modelos/EstadoJuego.php';
require_once __DIR__ . '/../modelos/Comentario.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../modelos/Logro.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// Inicialización de Modelos
$modelo_videojuego  = new Videojuego($conexion);
$modelo_estado      = new EstadoJuego($conexion);
$modelo_comentario  = new Comentario($conexion);
$modelo_notificacion = new Notificacion($conexion);
$modelo_logro       = new Logro($conexion);

$id_videojuego = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id_videojuego) { die("Videojuego no especificado."); }

$videojuego = $modelo_videojuego->obtenerPorId($id_videojuego);
$titulo_juego = $videojuego['titulo'] ?? 'este juego';

// ==========================================
// PROCESAMIENTO DE ACCIONES (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Acción: Actualizar Estado/Valoración
    if (isset($_POST['estado'])) {
        $nuevo_estado = !empty($_POST['estado']) ? $_POST['estado'] : null;
        $nueva_val    = isset($_POST['valoracion']) ? (int)$_POST['valoracion'] : null;

        $actual = $modelo_estado->obtenerEstadoUsuario($id_usuario, $id_videojuego);
        
        $modelo_estado->setEstado($id_usuario, $id_videojuego, $nuevo_estado, $nueva_val);
        $modelo_logro->comprobarLogros($id_usuario, $modelo_estado, $modelo_comentario);

        // Notificaciones sutiles
        if ($nuevo_estado && ($actual['estado'] ?? null) !== $nuevo_estado) {
            $modelo_notificacion->crear($id_usuario, 'juego', "Has cambiado el estado de $titulo_juego a $nuevo_estado.", $id_videojuego);
        } elseif ($nueva_val !== null && ($actual['valoracion'] ?? null) != $nueva_val) {
            $modelo_notificacion->crear($id_usuario, 'juego', "Has valorado $titulo_juego con $nueva_val estrellas.", $id_videojuego);
        }
        
        header("Location: detalle.php?id=$id_videojuego");
        exit;
    }

    // Acción: Insertar Comentario/Respuesta
    if (isset($_POST['contenido'])) {
        $contenido = trim($_POST['contenido']);
        $id_padre  = $_POST['id_comentario_padre'] ?? null;

        if ($contenido !== '') {
            $modelo_comentario->insertar($id_usuario, $id_videojuego, $contenido, $id_padre);
            $modelo_logro->comprobarLogros($id_usuario, $modelo_estado, $modelo_comentario);

            if ($id_padre) {
                $id_autor_padre = $modelo_comentario->obtenerAutor($id_padre);
                if ($id_autor_padre && $id_autor_padre != $id_usuario) {
                    $modelo_notificacion->crear($id_autor_padre, 'comentario', "Alguien ha respondido a tu comentario en $titulo_juego.", $id_videojuego);
                }
            } else {
                $modelo_notificacion->crear($id_usuario, 'juego', "Has comentado en $titulo_juego.", $id_videojuego);
            }
        }
        header("Location: detalle.php?id=$id_videojuego");
        exit;
    }
}

// ==========================================
// CARGA DE DATOS PARA LA VISTA (GET)
// ==========================================
$estado_actual      = $modelo_estado->obtenerEstadoUsuario($id_usuario, $id_videojuego);
$valoracion_usuario = $estado_actual['valoracion'] ?? 0;
$media_valoracion   = $modelo_estado->obtenerMediaValoracion($id_videojuego);
$comentarios        = $modelo_comentario->obtenerPorVideojuego($id_videojuego);

// Gestión de imagen modular
$ruta_img = $videojuego['imagen_portada'] ?? '';
$videojuego['imagen_portada_url'] = (!empty($ruta_img) && file_exists(__DIR__ . '/../../' . $ruta_img)) 
    ? '../../' . $ruta_img 
    : '../../frontend/assets/img_placeholder.png';

$titulo_pagina = $titulo_juego . " - GameSocial";

require_once __DIR__ . '/../../frontend/vistas/detalle.php';