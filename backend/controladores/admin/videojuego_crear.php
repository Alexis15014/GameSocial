<?php
/**
 * Controlador: admin/videojuego_crear.php
 * Propósito: Procesar la creación de un nuevo videojuego desde el panel admin.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/videojuego_admin.php';

requiereAdmin($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = obtenerDatosFormulario();

    $stmt = $conexion->prepare(
        "INSERT INTO videojuegos (titulo, descripcion, plataforma, genero, fecha_lanzamiento, desarrolladora, tipo)
         VALUES (:titulo, :descripcion, :plataforma, :genero, :fecha_lanzamiento, :desarrolladora, :tipo)"
    );
    $stmt->execute($datos);

    header("Location: videojuegos.php");
    exit;
}

$titulo_pagina = "Añadir Nuevo Videojuego | Panel Admin";
$videojuego    = [];
$modo          = 'crear';

require_once __DIR__ . '/../../../frontend/vistas/admin/videojuego_form.php';
