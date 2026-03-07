<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelos/Usuario.php';
require_once __DIR__ . '/../../modelos/Post.php';
require_once __DIR__ . '/../../modelos/PostRespuesta.php';
require_once __DIR__ . '/../../modelos/Videojuego.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔐 Seguridad: solo admins
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$modelo_usuario = new Usuario($conexion);
$modelo_post = new Post($conexion);
$modelo_respuesta = new PostRespuesta($conexion);
$modelo_juego = new Videojuego($conexion);

// ---------------------
// ACCIONES (Sin cambios)
// ---------------------
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

if ($accion === 'rol' && $id) {
    $modelo_usuario->cambiarRol($id);
    header("Location: admin.php");
    exit;
}

if ($accion === 'eliminar_usuario' && $id) {
    $modelo_usuario->eliminar($id);
    header("Location: admin.php");
    exit;
}

if ($accion === 'eliminar_post' && $id) {
    $modelo_post->eliminar($id);
    header("Location: admin.php");
    exit;
}

// ---------------------
// DATOS FILTRADOS (Usuarios y Posts)
// ---------------------
$busqueda_user = isset($_GET['q']) ? trim($_GET['q']) : '';
$busqueda_post = isset($_GET['qp']) ? trim($_GET['qp']) : '';

// 1. Lógica de Usuarios: Solo se filtra si 'q' tiene valor
if ($busqueda_user !== '') {
    $stmt_u = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario LIKE :q OR email LIKE :q ORDER BY nombre_usuario ASC");
    $stmt_u->execute([':q' => "%$busqueda_user%"]);
    $usuarios = $stmt_u->fetchAll(PDO::FETCH_ASSOC);
} else {
    $usuarios = $modelo_usuario->obtenerTodos();
}

// 2. Lógica de Posts: Solo se filtra si 'qp' tiene valor
// Buscamos tanto por contenido como por nombre de autor en la sección de posts
if ($busqueda_post !== '') {
    $sql_posts = "SELECT p.*, u.nombre_usuario, u.foto_perfil 
                  FROM posts p 
                  JOIN usuarios u ON p.id_usuario = u.id_usuario 
                  WHERE p.contenido LIKE :qp OR u.nombre_usuario LIKE :qp
                  ORDER BY p.fecha_creacion DESC";
    $stmt_p = $conexion->prepare($sql_posts);
    $stmt_p->execute([':qp' => "%$busqueda_post%"]);
    $posts = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no hay búsqueda de posts, traemos todos con su autor
    $posts = $conexion->query("SELECT p.*, u.nombre_usuario, u.foto_perfil 
                               FROM posts p 
                               JOIN usuarios u ON p.id_usuario = u.id_usuario 
                               ORDER BY p.fecha_creacion DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$juegos = $modelo_juego->obtenerTodos();
$busqueda = $busqueda_user; // Para compatibilidad con tu vista

$titulo_pagina = "Panel de Administración General | GameSocial";

require_once __DIR__ . '/../../../frontend/vistas/admin/admin.php';