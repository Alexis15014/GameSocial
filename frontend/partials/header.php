<?php
/**
 * Componente: Header (Vista)
 * Propósito: Renderizar la barra de navegación respetando el diseño original.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/config/conexion.php';
require_once __DIR__ . '/../../backend/modelos/Usuario.php';
require_once __DIR__ . '/../../backend/modelos/Notificacion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$modelo_notificacion = new Notificacion($conexion);

// Obtenemos notificaciones y contamos las nuevas
$notificaciones_usuario = $id_usuario ? $modelo_notificacion->obtenerPorUsuario($id_usuario) : [];
$nuevas = 0;
foreach ($notificaciones_usuario as $n) {
    if ($n['leida'] == 0) $nuevas++;
}

// Capturamos la URL actual de forma limpia para los retornos
$url_retorno = urlencode($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo_pagina ?? "GameSocial"; ?></title>
    <link rel="icon" type="image/png" href="<?php echo $favicon_url ?? '/gamesocial/frontend/assets/img/gamesocial.png'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link rel="stylesheet" href="/gamesocial/frontend/assets/css/custom.css">
</head>
<body>

<nav class="barra-navegacion navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="marca navbar-brand d-flex align-items-center" href="/gamesocial/backend/controladores/feed.php">
            <img src="/gamesocial/frontend/assets/img/gamesocial.png" alt="Logo GameSocial" class="me-2 logo-navbar">
            GameSocial
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#contenidoNavbar"
                aria-controls="contenidoNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="contenidoNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/feed.php">Inicio</a></li>
                <li class="nav-item"><a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/catalogo.php">Catálogo</a></li>
                <li class="nav-item"><a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/mis_juegos.php">Mis juegos</a></li>
                <li class="nav-item"><a class="enlace-nav nav-link" href="/gamesocial/backend/controladores/perfil.php">Perfil</a></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li class="nav-item"><a href="/gamesocial/backend/controladores/admin/admin.php" class="enlace-nav nav-link">Panel Admin</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <form class="form-buscador d-flex" method="GET" action="/gamesocial/backend/controladores/buscar_usuarios.php">
                    <input class="form-control form-control-sm input-buscador" type="search" name="q" placeholder="Buscar usuarios" aria-label="Buscar usuarios">
                </form>
                
                <li class="nav-item dropdown">
                    <a class="enlace-notificaciones nav-link position-relative" href="#" id="desplegableNotificaciones" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        🔔
                        <?php if ($nuevas > 0): ?>
                            <span class="badge-notificacion position-absolute top-0 start-100 translate-middle"><?php echo $nuevas; ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="desplegable-notificaciones dropdown-menu dropdown-menu-end" aria-labelledby="desplegableNotificaciones">
                        <?php if (empty($notificaciones_usuario)): ?>
                            <li class="dropdown-item text-light">No tienes notificaciones</li>
                        <?php else: ?>
                            <?php foreach ($notificaciones_usuario as $notificacion): ?>
                                <li class="elemento-notificacion d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="mensaje"><?php echo htmlspecialchars($notificacion['mensaje']); ?></small><br>
                                        <small class="fecha"><?php echo $notificacion['fecha_notificacion']; ?></small>
                                    </div>
                                    <div class="acciones-notificacion d-flex flex-column">
                                        <?php if ($notificacion['leida'] == 0): ?>
                                            <a href="/gamesocial/backend/controladores/notificaciones.php?marcar_leida=<?php echo $notificacion['id_notificacion']; ?>&redirect=<?php echo $url_retorno; ?>" class="btn btn-sm btn-success">Leída</a>
                                        <?php endif; ?>
                                        <a href="/gamesocial/backend/controladores/notificaciones.php?eliminar=<?php echo $notificacion['id_notificacion']; ?>&redirect=<?php echo $url_retorno; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                                    </div>
                                </li>
                                <hr class="dropdown-divider">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="/gamesocial/backend/controladores/logout.php"><i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>