<?php
session_start();

// Seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

// ===========================
// Recoger filtros (Igual que en catálogo)
// ===========================
$busqueda   = trim($_GET['q'] ?? '');
$plataforma = trim($_GET['plataforma'] ?? '');

// ===========================
// Construir consulta dinámica
// ===========================
$sql = "SELECT * FROM videojuegos WHERE 1=1";
$params = [];

// Filtro por Título
if ($busqueda !== '') {
    $sql .= " AND titulo LIKE :busqueda";
    $params[':busqueda'] = '%' . $busqueda . '%';
}

// Filtro por Plataforma
if ($plataforma !== '') {
    $sql .= " AND plataforma LIKE :plataforma";
    $params[':plataforma'] = '%' . $plataforma . '%';
}

$sql .= " ORDER BY titulo ASC";

// Ejecutar
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$videojuegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo_pagina = "Gestión de Videojuegos | Panel de Control";

// Cargar la vista
require_once __DIR__ . '/../../../frontend/vistas/admin/videojuegos.php';