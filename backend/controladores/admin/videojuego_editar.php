<?php
/**
 * Controlador: admin/videojuego_editar.php
 * Propósito: Procesar la edición de un videojuego existente desde el panel admin.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/videojuego_admin.php';

requiereAdmin($conexion);

$id_videojuego = $_GET['id'] ?? null;
if (!$id_videojuego) {
    die("ID de videojuego no válido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos        = obtenerDatosFormulario();
    $datos[':id'] = $id_videojuego;

    $stmt = $conexion->prepare(
        "UPDATE videojuegos
         SET titulo = :titulo, descripcion = :descripcion, plataforma = :plataforma,
             genero = :genero, fecha_lanzamiento = :fecha_lanzamiento,
             desarrolladora = :desarrolladora, tipo = :tipo
         WHERE id_videojuego = :id"
    );
    $stmt->execute($datos);

    header("Location: videojuegos.php");
    exit;
}

// Cargamos los datos actuales para pre-rellenar el formulario
$stmt_carga = $conexion->prepare("SELECT * FROM videojuegos WHERE id_videojuego = ?");
$stmt_carga->execute([$id_videojuego]);
$videojuego = $stmt_carga->fetch(PDO::FETCH_ASSOC);

$titulo_pagina = "Editando: " . htmlspecialchars($videojuego['titulo']) . " | Panel Admin";
$modo          = 'editar';

require_once __DIR__ . '/../../../frontend/vistas/admin/videojuego_form.php';
