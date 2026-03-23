<?php
/**
 * Componente: header_admin.php
 * Propósito: Renderizar la cabecera y barra de navegación del panel de administración.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../backend/helpers/auth.php';

// Verificamos que sea admin antes de renderizar nada del panel
// Usamos global para acceder a $conexion creada en el controlador padre
global $conexion;
requiereAdmin($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?? 'Panel Admin | GameSocial' ?></title>
    <link rel="icon" type="image/png" href="<?= $favicon_url ?? '/gamesocial/frontend/assets/img/gamesocial.png' ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/gamesocial/frontend/assets/css/custom.css">
</head>
<body>

<!-- Barra de navegación del panel admin -->
<nav class="barra-navegacion navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="marca navbar-brand d-flex align-items-center" href="/gamesocial/backend/controladores/admin/admin.php">
            <img src="/gamesocial/frontend/assets/img/gamesocial.png" alt="Logo GameSocial" class="me-2 logo-navbar">
            Admin Panel
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin"
                aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/admin/admin.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/admin/videojuegos.php">Videojuegos</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/feed.php">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
