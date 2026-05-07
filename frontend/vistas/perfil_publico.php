<?php
/**
 * Vista: perfil_publico.php
 * Propósito: Visualización de perfil público con listas, estadísticas de colección y logros.
 */
require_once __DIR__ . '/../../backend/config/estados_juego.php';
include __DIR__ . '/../partials/header.php';

$total_juegos_pub = array_sum($stats_estados);
?>

<div class="container mt-4 perfil-container">
    <div class="text-center mb-4">
        <h3 class="gamesocial-perfil-title"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h3>

        <!-- Avatar del usuario -->
        <div class="avatar-wrapper my-3">
            <img
                src="<?= resolverAvatar($usuario['foto_perfil'] ?? null) ?>"
                class="img-perfil-circulo shadow-lg"
                alt="Avatar de <?= htmlspecialchars($usuario['nombre_usuario']) ?>"
            >
        </div>

        <p class="text-light">
            <span class="stat-highlight"><strong><?= $seguidores ?></strong></span> seguidores ·
            <span class="stat-highlight"><strong><?= $seguidos ?></strong></span> siguiendo
        </p>

        <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != $usuario['id_usuario']): ?>
            <div class="mt-3">
                <a href="/gamesocial/follow?id=<?= $usuario['id_usuario'] ?>"
                   class="btn-detalle <?= $esta_siguiendo ? 'btn-unfollow' : '' ?>">
                    <?= $esta_siguiendo ? 'Dejar de seguir' : 'Seguir a este Gamer' ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <hr class="border-secondary my-4">

    <!-- Estadísticas de colección -->
    <?php if ($total_juegos_pub > 0): ?>
        <h5 class="section-subtitle mb-4">📊 Colección de <?= htmlspecialchars($usuario['nombre_usuario']) ?></h5>

        <div class="row g-4 align-items-center mb-5">
            <div class="col-12 col-md-5 text-center">
                <canvas id="graficoEstadosPub" style="max-height:240px;"
                    data-labels='<?= json_encode(array_keys($stats_estados)) ?>'
                    data-datos='<?= json_encode(array_values($stats_estados)) ?>'
                    data-colores='<?= json_encode(array_values($estados_colores_chart)) ?>'
                    data-etiquetas='<?= json_encode($estados_etiquetas) ?>'
                ></canvas>
                <p class="text-muted small mt-2 mb-0">
                    <i class="fas fa-gamepad me-1"></i>
                    <?= $total_juegos_pub ?> juego<?= $total_juegos_pub != 1 ? 's' : '' ?> en su biblioteca
                </p>
            </div>
            <div class="col-12 col-md-7">
                <div class="row g-2 leyenda-grafico">
                    <?php foreach ($stats_estados as $clave => $cantidad): ?>
                        <?php if ($cantidad > 0): ?>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded"
                                 style="background:rgba(255,255,255,0.04); border-left:3px solid <?= $estados_colores_chart[$clave] ?>;">
                                <span class="fw-bold fs-5" style="color:<?= $estados_colores_chart[$clave] ?>; font-family:'Orbitron',sans-serif;">
                                    <?= $cantidad ?>
                                </span>
                                <span class="small text-light"><?= $estados_etiquetas[$clave] ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="/gamesocial/frontend/assets/js/perfil_publico.js"></script>

        <hr class="border-secondary my-4">
    <?php endif; ?>

    <!-- Listas públicas -->
    <?php if (!empty($listas_publicas)): ?>
        <h5 class="section-subtitle mb-4">📋 Listas de <?= htmlspecialchars($usuario['nombre_usuario']) ?></h5>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-5">
            <?php foreach ($listas_publicas as $lista): ?>
                <div class="col">
                    <div class="tarjeta-videojuego h-100">

                        <div class="imagen-wrapper">
                            <?php if ($lista['portada_url']): ?>
                                <img src="<?= htmlspecialchars($lista['portada_url']) ?>"
                                     class="imagen-tarjeta"
                                     alt="Portada de <?= htmlspecialchars($lista['nombre']) ?>">
                            <?php else: ?>
                                <div class="lista-portada-placeholder">
                                    <i class="fas fa-list fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="cuerpo-tarjeta p-3 d-flex flex-column flex-grow-1">
                            <h5 class="titulo-tarjeta mb-2 text-truncate"
                                title="<?= htmlspecialchars($lista['nombre']) ?>">
                                <?= htmlspecialchars($lista['nombre']) ?>
                            </h5>

                            <?php if ($lista['descripcion']): ?>
                                <p class="small text-muted mb-2 lista-desc"><?= htmlspecialchars($lista['descripcion']) ?></p>
                            <?php endif; ?>

                            <div class="mb-3">
                                <span class="badge bg-secondary">
                                    <i class="fas fa-gamepad me-1"></i>
                                    <?= (int)$lista['total_juegos'] ?> juego<?= $lista['total_juegos'] != 1 ? 's' : '' ?>
                                </span>
                            </div>

                            <div class="mt-auto">
                                <a href="/gamesocial/lista/<?= $lista['id_lista'] ?>-<?= generarSlug($lista['nombre']) ?>"
                                   class="btn-detalle w-100 text-center">
                                    Ver lista
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="border-secondary my-4">
    <?php endif; ?>

    <!-- Logros -->
    <h5 class="section-subtitle mb-4">🏆 Vitrina de Logros</h5>

    <?php if (!empty($logros)): ?>
        <div class="row g-3">
            <?php foreach ($logros as $logro): ?>
                <div class="col-6 col-md-4">
                    <div class="tarjeta-logro text-center p-3 h-100">
                        <div class="icono-logro">🏅</div>
                        <div class="nombre-logro"><?= htmlspecialchars($logro['nombre']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-dark text-center">Este usuario aún está en nivel 1. No tiene logros registrados.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
