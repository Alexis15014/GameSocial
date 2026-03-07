<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID no válido");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipos_validos = ['juego_base', 'dlc', 'expansion', 'edicion_especial', 'remake', 'remaster'];
    $tipo = in_array($_POST['tipo'] ?? '', $tipos_validos) ? $_POST['tipo'] : 'juego_base';

    $stmt = $conexion->prepare(
        "UPDATE videojuegos
         SET titulo = ?, descripcion = ?, plataforma = ?, genero = ?, 
             fecha_lanzamiento = ?, desarrolladora = ?, tipo = ?
         WHERE id_videojuego = ?"
    );
    $stmt->execute([
        $_POST['titulo'],
        $_POST['descripcion'],
        $_POST['plataforma'],
        $_POST['genero'],
        $_POST['fecha_lanzamiento'],
        $_POST['desarrolladora'],
        $tipo,
        $id
    ]);

    header("Location: videojuegos.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM videojuegos WHERE id_videojuego = ?");
$stmt->execute([$id]);
$videojuego = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre_juego = htmlspecialchars($videojuego['titulo']);
$titulo_pagina = "Editando: " . $nombre_juego . " | Panel Admin";

$modo = 'editar';
require_once __DIR__ . '/../../../frontend/vistas/admin/videojuego_form.php';
