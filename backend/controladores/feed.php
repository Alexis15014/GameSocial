<?php
/**
 * Controlador: feed.php
 * Propósito: Gestionar la publicación de posts, respuestas y la carga del muro social.
 * Proyecto: GameSocial
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Post.php';
require_once __DIR__ . '/../modelos/PostRespuesta.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../modelos/Like.php';

// --- 1. VERIFICACIÓN DE SESIÓN ---
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

// Instancia de Modelos
$modelo_post         = new Post($conexion);
$modelo_respuesta    = new PostRespuesta($conexion);
$modelo_notificacion = new Notificacion($conexion);
$modelo_like         = new Like($conexion);

// --- 2. PROCESAR PUBLICACIONES (POST O RESPUESTA) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenido'])) {
    $contenido = trim($_POST['contenido']);
    $id_post = $_POST['id_post'] ?? null;
    $id_respuesta_padre = $_POST['id_respuesta_padre'] ?? null;

    if ($id_post) {
        // --- Caso A: Es una Respuesta ---
        $modelo_respuesta->insertar($id_post, $id_usuario, $contenido, $id_respuesta_padre);

        // Notificar al autor del post original (si no es el mismo usuario)
        $id_autor_post = $modelo_post->obtenerAutor($id_post);
        if ($id_autor_post && $id_autor_post != $id_usuario) {
            $modelo_notificacion->crear(
                $id_autor_post,
                'post',
                '¡Alguien ha comentado en tu publicación!'
            );
        }
    } else {
        // --- Caso B: Es un Post Nuevo ---
        $modelo_post->insertar($id_usuario, $contenido);
        // Notificación de confirmación para el propio usuario
        $modelo_notificacion->crear($id_usuario, 'post', 'Tu post se ha publicado correctamente.');
    }

    header("Location: feed.php");
    exit;
}

// --- 3. OBTENER Y ENRIQUECER DATOS DEL MURO ---
// Obtenemos todos los posts de la base de datos
$posts = $modelo_post->obtenerTodos() ?: [];

/**
 * Recorremos cada post para añadirle información adicional 
 * que la vista necesitará (Likes y sus Respuestas correspondientes).
 */
foreach ($posts as &$post) {
    // Obtener cantidad de likes del post
    $post['likes'] = $modelo_like->contar('post', $post['id_post']);

    // Obtener todas las respuestas asociadas a este post
    $post['respuestas'] = $modelo_respuesta->obtenerPorPost($post['id_post']) ?: [];

    // Enriquecer cada respuesta con sus propios likes
    foreach ($post['respuestas'] as &$respuesta) {
        $respuesta['likes'] = $modelo_like->contar('respuesta', $respuesta['id_respuesta']);
    }
}

// Configuración de la vista
$titulo_pagina = "Inicio - Comunidad GameSocial";

// Carga de la interfaz (Frontend)
require_once __DIR__ . '/../../frontend/vistas/feed.php';