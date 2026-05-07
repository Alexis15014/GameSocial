<?php
/**
 * Controlador: admin/admin.php
 * Propósito: Panel de administración general: gestión de usuarios y posts.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelos/Usuario.php';
require_once __DIR__ . '/../../modelos/Post.php';
require_once __DIR__ . '/../../modelos/Videojuego.php';
require_once __DIR__ . '/../../helpers/auth.php';

requiereAdmin($conexion);

$modelo_usuario = new Usuario($conexion);
$modelo_post    = new Post($conexion);
$modelo_juego   = new Videojuego($conexion);

// --- ACCIONES ---

$accion    = $_GET['accion'] ?? null;
$id_accion = $_GET['id']     ?? null;

if ($accion === 'rol' && $id_accion) {
    $modelo_usuario->cambiarRol($id_accion);
    header("Location: /gamesocial/admin");
    exit;
}

if ($accion === 'eliminar_usuario' && $id_accion) {
    $modelo_usuario->eliminar($id_accion);
    header("Location: /gamesocial/admin");
    exit;
}

if ($accion === 'eliminar_post' && $id_accion) {
    $modelo_post->eliminar($id_accion);
    header("Location: /gamesocial/admin");
    exit;
}

// --- DATOS CON FILTROS OPCIONALES ---

$busqueda_user = trim($_GET['q']  ?? '');
$busqueda_post = trim($_GET['qp'] ?? '');

// Filtramos usuarios por nombre o email si hay búsqueda activa
if ($busqueda_user !== '') {
    $stmt_usuarios = $conexion->prepare(
        "SELECT * FROM usuarios
         WHERE nombre_usuario LIKE :q OR email LIKE :q
         ORDER BY nombre_usuario ASC"
    );
    $stmt_usuarios->execute([':q' => "%$busqueda_user%"]);
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
} else {
    $usuarios = $modelo_usuario->obtenerTodos();
}

// Filtramos posts por contenido o nombre de autor si hay búsqueda activa
if ($busqueda_post !== '') {
    $sql_posts_filtrados = "SELECT p.*, u.nombre_usuario, u.foto_perfil
                            FROM posts p
                            JOIN usuarios u ON p.id_usuario = u.id_usuario
                            WHERE p.contenido LIKE :qp OR u.nombre_usuario LIKE :qp
                            ORDER BY p.fecha_creacion DESC";
    $stmt_posts = $conexion->prepare($sql_posts_filtrados);
    $stmt_posts->execute([':qp' => "%$busqueda_post%"]);
    $posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
} else {
    $posts = $conexion->query(
        "SELECT p.*, u.nombre_usuario, u.foto_perfil
         FROM posts p
         JOIN usuarios u ON p.id_usuario = u.id_usuario
         ORDER BY p.fecha_creacion DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

$titulo_pagina = "Panel de Administración General | GameSocial";

require __DIR__ . '/../../../frontend/vistas/admin/admin.php';
