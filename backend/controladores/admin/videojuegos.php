<?php
/**
 * Controlador: admin/videojuegos.php
 * Propósito: Listar y filtrar el catálogo de videojuegos en el panel de administración.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';

requiereAdmin($conexion);

$busqueda   = trim($_GET['q']          ?? '');
$plataforma = trim($_GET['plataforma'] ?? '');

$sql    = "SELECT * FROM videojuegos WHERE 1=1";
$params = [];

if ($busqueda !== '') {
    $sql .= " AND titulo LIKE :busqueda";
    $params[':busqueda'] = '%' . $busqueda . '%';
}

if ($plataforma !== '') {
    $sql .= " AND plataforma LIKE :plataforma";
    $params[':plataforma'] = '%' . $plataforma . '%';
}

$sql .= " ORDER BY titulo ASC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$videojuegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo_pagina = "Gestión de Videojuegos | Panel de Control";

require_once __DIR__ . '/../../../frontend/vistas/admin/videojuegos.php';
