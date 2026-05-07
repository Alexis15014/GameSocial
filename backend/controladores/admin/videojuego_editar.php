<?php
/**
 * Controlador: admin/videojuego_editar.php
 * Propósito: Procesar la edición de un videojuego existente desde el panel admin.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/slug.php';
require_once __DIR__ . '/../../helpers/videojuego_admin.php';

requiereAdmin($conexion);

// Aceptamos slug (nombre-juego) o ID numérico
$param = $_GET['id'] ?? null;
if (!$param) { die("Videojuego no especificado."); }

if (is_numeric($param)) {
    $id_videojuego = (int)$param;
} else {
    $stmt_t = $conexion->query("SELECT id_videojuego, titulo FROM videojuegos");
    $id_videojuego = null;
    foreach ($stmt_t->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (generarSlug($row['titulo']) === $param) {
            $id_videojuego = (int)$row['id_videojuego'];
            break;
        }
    }
    if (!$id_videojuego) { http_response_code(404); die("<h1>Videojuego no encontrado.</h1>"); }
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

    header("Location: /gamesocial/admin/videojuegos");
    exit;
}

// Cargamos los datos actuales para pre-rellenar el formulario
$stmt_carga = $conexion->prepare("SELECT * FROM videojuegos WHERE id_videojuego = ?");
$stmt_carga->execute([$id_videojuego]);
$videojuego = $stmt_carga->fetch(PDO::FETCH_ASSOC);

$titulo_pagina = "Editando: " . htmlspecialchars($videojuego['titulo']) . " | Panel Admin";
$modo          = 'editar';

require __DIR__ . '/../../../frontend/vistas/admin/videojuego_form.php';
