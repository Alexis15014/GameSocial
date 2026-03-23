<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-4 contenedor-catalogo mb-4">
    <h2 class="mb-4 text-center catalogo-titulo">CATÁLOGO DE VIDEOJUEGOS</h2>

    <form method="GET" class="form-busqueda mb-4">
        <div class="input-group input-busqueda-group">
            <span class="input-group-text bg-dark border-purple border-end-0"><i class="fas fa-search text-white"></i></span>
            <input
                type="text"
                name="q"
                class="form-control input-busqueda border-start-0"
                placeholder="Buscar videojuegos..."
                value="<?= htmlspecialchars($busqueda ?? '') ?>"
                autofocus
            >
            <button type="submit" class="btn-busqueda">Buscar</button>
        </div>
    </form>

    <div class="filtros-seccion mb-4 p-4 shadow-sm">
        <div class="row">
            <div class="col-12 col-md-4 mb-3 border-end-divider">
                <strong class="d-block mb-2"><i class="fas fa-ghost text-purple"></i> Género</strong>
                <div class="d-flex flex-wrap gap-1">
                    <?php
                    $generos = [];
                    foreach ($videojuegos as $v) {
                        foreach (explode(',', $v['genero']) as $g) $generos[trim($g)] = true;
                    }
                    ksort($generos);
                    foreach (array_keys($generos) as $g): ?>
                        <a href="?genero=<?= urlencode($g) ?>" class="btn-filtro"><?= htmlspecialchars($g) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3 border-end-divider">
                <strong class="d-block mb-2"><i class="fas fa-gamepad text-purple"></i> Plataforma</strong>
                <div class="d-flex flex-wrap gap-1">
                    <?php
                    $plataformas = [];
                    foreach ($videojuegos as $v) {
                        foreach (explode(',', $v['plataforma']) as $p) $plataformas[trim($p)] = true;
                    }
                    ksort($plataformas);
                    foreach (array_keys($plataformas) as $p): ?>
                        <a href="?plataforma=<?= urlencode($p) ?>" class="btn-filtro"><?= htmlspecialchars($p) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3 border-end-divider">
                <strong class="d-block mb-2"><i class="fas fa-building text-purple"></i> Desarrolladora</strong>
                <div class="d-flex flex-wrap gap-1">
                    <?php
                    $devs = [];
                    foreach ($videojuegos as $v) $devs[trim($v['desarrolladora'])] = true;
                    ksort($devs);
                    foreach (array_keys($devs) as $d): ?>
                        <a href="?desarrolladora=<?= urlencode($d) ?>" class="btn-filtro"><?= htmlspecialchars($d) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <strong class="d-block mb-2"><i class="fas fa-layer-group text-purple"></i> Tipo</strong>
                <div class="d-flex flex-wrap gap-1">
                    <?php
                    $tipos_catalogo = [
                        'juego_base'       => 'Juego base',
                        'dlc'              => 'DLC',
                        'expansion'        => 'Expansión',
                        'edicion_especial' => 'Edición especial',
                        'remake'           => 'Remake',
                        'remaster'         => 'Remaster',
                    ];
                    $tipos_presentes = array_unique(array_column($videojuegos, 'tipo'));
                    foreach ($tipos_catalogo as $val => $etiqueta):
                        if (in_array($val, $tipos_presentes)): ?>
                            <a href="?tipo=<?= urlencode($val) ?>" class="btn-filtro"><?= $etiqueta ?></a>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
        </div>
        <div class="text-center mt-3 pt-3 border-top border-purple-dark">
            <a href="catalogo.php" class="btn-reset btn-sm text-decoration-none">
                <i class="fas fa-undo"></i> Restablecer todos los filtros
            </a>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($videojuegos as $videojuego): ?>
            <div class="col">
                <div class="tarjeta-videojuego h-100">
                    <div class="imagen-wrapper">
                        <img 
                            src="<?= htmlspecialchars($videojuego['imagen_portada_url']); ?>" 
                            class="imagen-tarjeta"
                            alt="<?= htmlspecialchars($videojuego['titulo']); ?>"
                        >
                        <div class="overlay-fecha">
                            <?= date('Y', strtotime($videojuego['fecha_lanzamiento'])) ?>
                        </div>
                    </div>

                    <div class="cuerpo-tarjeta p-3 d-flex flex-column flex-grow-1">
                        <h5 class="titulo-tarjeta mb-2 text-truncate" title="<?= htmlspecialchars($videojuego['titulo']); ?>">
                            <?= htmlspecialchars($videojuego['titulo']); ?>
                        </h5>

                        <div class="info-meta mb-3">
                            <span class="d-block small text-truncate text-muted mb-1">
                                <i class="fas fa-vr-cardboard text-purple me-1"></i> <?= htmlspecialchars($videojuego['plataforma']); ?>
                            </span>
                            <span class="d-block small text-truncate text-muted mb-1">
                                <i class="fas fa-tag text-purple me-1"></i> <?= htmlspecialchars($videojuego['genero']); ?>
                            </span>
                            <span class="d-block small text-truncate text-muted mb-1">
                                <i class="fas fa-building text-purple me-1"></i> <?= htmlspecialchars($videojuego['desarrolladora']); ?>
                            </span>
                            <?php
                            $tipo_val = $videojuego['tipo'] ?? 'juego_base';
                            $tipo_colores = [
                                'juego_base'       => 'badge-tipo-base',
                                'dlc'              => 'badge-tipo-dlc',
                                'expansion'        => 'badge-tipo-expansion',
                                'edicion_especial' => 'badge-tipo-especial',
                                'remake'           => 'badge-tipo-remake',
                                'remaster'         => 'badge-tipo-remaster',
                            ];
                            $tipo_etiquetas = [
                                'juego_base'       => 'Juego base',
                                'dlc'              => 'DLC',
                                'expansion'        => 'Expansión',
                                'edicion_especial' => 'Edición especial',
                                'remake'           => 'Remake',
                                'remaster'         => 'Remaster',
                            ];
                            $clase_tipo = $tipo_colores[$tipo_val] ?? 'badge-tipo-base';
                            $etiqueta_tipo = $tipo_etiquetas[$tipo_val] ?? ucfirst($tipo_val);
                            ?>
                            <span class="badge-tipo <?= $clase_tipo ?>"><?= $etiqueta_tipo ?></span>
                        </div>

                        <a href="detalle.php?id=<?= $videojuego['id_videojuego']; ?>" class="btn-detalle mt-auto">
                            Ver detalle
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>