<?php 
/**
 * Vista: perfil_publico.php
 * Propósito: Visualización de perfil público con listas, estadísticas de colección y logros.
 */
include __DIR__ . '/../partials/header.php';

$estados_etiquetas_pub = [
    'sin_iniciar' => 'Sin iniciar',
    'inacabado'   => 'Inacabado',
    'terminado'   => 'Terminado',
    'completado'  => 'Completado',
    'en_curso' => 'En curso',
    'abandonado'  => 'Abandonado',
];
$estados_colores_pub = [
    'sin_iniciar' => '#00C8FF',
    'inacabado'   => '#FF8C00',
    'terminado'   => '#9D4EDD',
    'completado'  => '#00B37E',
    'en_curso' => '#00C49A',
    'abandonado'  => '#FF3D5A',
];
$total_juegos_pub = array_sum($stats_estados);
?>

<div class="container mt-4 perfil-container">
    <div class="text-center mb-4">
        <h3 class="gamesocial-perfil-title"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h3>
        
        <p class="text-light">
            <span class="stat-highlight"><strong><?= $seguidores ?></strong></span> seguidores ·
            <span class="stat-highlight"><strong><?= $seguidos ?></strong></span> siguiendo
        </p>

        <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != $usuario['id_usuario']): ?>
            <div class="mt-3">
                <a href="/gamesocial/backend/controladores/follow.php?id=<?= $usuario['id_usuario'] ?>"
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
                <canvas id="graficoEstadosPub" style="max-height:240px;"></canvas>
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
                                 style="background:rgba(255,255,255,0.04); border-left:3px solid <?= $estados_colores_pub[$clave] ?>;">
                                <span class="fw-bold fs-5" style="color:<?= $estados_colores_pub[$clave] ?>; font-family:'Orbitron',sans-serif;">
                                    <?= $cantidad ?>
                                </span>
                                <span class="small text-light"><?= $estados_etiquetas_pub[$clave] ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const rawLabels  = <?= json_encode(array_keys($stats_estados)) ?>;
            const rawDatos   = <?= json_encode(array_values($stats_estados)) ?>;
            const rawColores = <?= json_encode(array_values($estados_colores_pub)) ?>;
            const etiquetas  = <?= json_encode($estados_etiquetas_pub) ?>;

            const labels = [], datos = [], colores = [];
            rawLabels.forEach(function(k, i) {
                if (rawDatos[i] > 0) {
                    labels.push(etiquetas[k] || k);
                    datos.push(rawDatos[i]);
                    colores.push(rawColores[i]);
                }
            });

            new Chart(document.getElementById('graficoEstadosPub'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{ data: datos, backgroundColor: colores, borderColor: '#1F1B24', borderWidth: 3, hoverOffset: 10 }]
                },
                options: {
                    responsive: true, cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F1B24', borderColor: '#3E2D50', borderWidth: 1,
                            titleColor: '#FBB040', bodyColor: '#F5F5F5',
                            callbacks: {
                                label: function(ctx) {
                                    const total = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                                    const pct = ((ctx.parsed/total)*100).toFixed(1);
                                    return '  ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        })();
        </script>

        <hr class="border-secondary my-4">
    <?php endif; ?>

    <!-- Listas públicas -->
    <?php if (!empty($listas_publicas)): ?>
        <h5 class="section-subtitle mb-4">📋 Listas de <?= htmlspecialchars($usuario['nombre_usuario']) ?></h5>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-lg-3 g-4 mb-5">
            <?php foreach ($listas_publicas as $lista): ?>
                <div class="col">
                    <a href="/gamesocial/backend/controladores/listas.php?ver=<?= $lista['id_lista'] ?>"
                       class="text-decoration-none">
                        <div class="tarjeta-lista h-100">
                            <div class="portada-lista-wrapper">
                                <?php if ($lista['portada_url']): ?>
                                    <img src="<?= htmlspecialchars($lista['portada_url']) ?>"
                                         class="portada-lista-img"
                                         alt="Portada de <?= htmlspecialchars($lista['nombre']) ?>">
                                <?php else: ?>
                                    <div class="portada-lista-placeholder">
                                        <i class="fas fa-list fa-2x text-purple opacity-50"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="portada-lista-overlay">
                                    <span class="portada-lista-count">
                                        <i class="fas fa-gamepad me-1"></i>
                                        <?= (int)$lista['total_juegos'] ?> juego<?= $lista['total_juegos'] != 1 ? 's' : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="tarjeta-lista-body">
                                <h5 class="titulo-tarjeta mb-1 text-truncate">
                                    <?= htmlspecialchars($lista['nombre']) ?>
                                </h5>
                                <?php if ($lista['descripcion']): ?>
                                    <p class="small text-muted mb-0 lista-desc">
                                        <?= htmlspecialchars($lista['descripcion']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
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
