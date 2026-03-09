<?php
/**
 * Controlador: feed.php
 * Propósito: Gestionar la publicación de posts y respuestas, y cargar el muro social.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Post.php';
require_once __DIR__ . '/../modelos/PostRespuesta.php';
require_once __DIR__ . '/../modelos/Notificacion.php';
require_once __DIR__ . '/../modelos/Like.php';
require_once __DIR__ . '/../helpers/auth.php';

$id_usuario = requiereLogin($conexion);

$modelo_post         = new Post($conexion);
$modelo_respuesta    = new PostRespuesta($conexion);
$modelo_notificacion = new Notificacion($conexion);
$modelo_like         = new Like($conexion);

// --- PROCESAMIENTO DE PUBLICACIONES (POST) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenido'])) {
    $contenido          = trim($_POST['contenido']);
    $id_post_padre      = $_POST['id_post']            ?? null;
    $id_respuesta_padre = $_POST['id_respuesta_padre'] ?? null;

    if ($id_post_padre) {
        // Caso A: es una respuesta a un post existente
        $modelo_respuesta->insertar($id_post_padre, $id_usuario, $contenido, $id_respuesta_padre);

        // Notificamos al autor del post si no es el mismo que responde
        $id_autor_post = $modelo_post->obtenerAutor($id_post_padre);
        if ($id_autor_post && $id_autor_post != $id_usuario) {
            $modelo_notificacion->crear($id_autor_post, 'post', '¡Alguien ha comentado en tu publicación!');
        }
    } else {
        // Caso B: es un post nuevo en el muro
        $modelo_post->insertar($id_usuario, $contenido);
        $modelo_notificacion->crear($id_usuario, 'post', 'Tu post se ha publicado correctamente.');
    }

    header("Location: feed.php");
    exit;
}

// --- CARGA DEL MURO ---

$posts = $modelo_post->obtenerTodos() ?: [];

// Enriquecemos cada post con sus likes y respuestas anidadas
foreach ($posts as &$post) {
    $post['likes']      = $modelo_like->contar('post', $post['id_post']);
    $post['respuestas'] = $modelo_respuesta->obtenerPorPost($post['id_post']) ?: [];

    foreach ($post['respuestas'] as &$respuesta) {
        $respuesta['likes'] = $modelo_like->contar('respuesta', $respuesta['id_respuesta']);
    }
    unset($respuesta);
}
unset($post);

$titulo_pagina = "Inicio - Comunidad GameSocial";

require_once __DIR__ . '/../../frontend/vistas/feed.php';
