<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina : "GameSocial"; ?></title>
    <link rel="icon" type="image/png" href="<?php echo isset($favicon_url) ? $favicon_url : '/gamesocial/frontend/assets/img/gamesocial.png'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/../gamesocial/frontend/assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- ===========================
     Barra de navegación admin
=========================== -->
<nav class="barra-navegacion navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
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

