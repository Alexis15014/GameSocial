<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipos_validos = ['juego_base', 'dlc', 'expansion', 'edicion_especial', 'remake', 'remaster'];
    $tipo = in_array($_POST['tipo'] ?? '', $tipos_validos) ? $_POST['tipo'] : 'juego_base';

    $stmt = $conexion->prepare(
        "INSERT INTO videojuegos (titulo, descripcion, plataforma, genero, fecha_lanzamiento, desarrolladora, tipo)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['titulo'],
        $_POST['descripcion'],
        $_POST['plataforma'],
        $_POST['genero'],
        $_POST['fecha_lanzamiento'],
        $_POST['desarrolladora'],
        $tipo
    ]);

    header("Location: videojuegos.php");
    exit;
}

$titulo_pagina = "Añadir Nuevo Videojuego | Panel Admin";

$modo = 'crear';
require_once __DIR__ . '/../../../frontend/vistas/admin/videojuego_form.php';
