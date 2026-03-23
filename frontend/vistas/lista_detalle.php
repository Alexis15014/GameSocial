<?php
/**
 * Vista: lista_detalle.php
 * Propósito: Mostrar el detalle de una lista y permitir añadir/quitar juegos.
 */
include __DIR__ . '/../partials/header.php';

$tipo_etiquetas = [
    'dlc'              => 'DLC',
    'expansion'        => 'Expansión',
    'edicion_especial' => 'Edición especial',
    'remake'           => 'Remake',
    'remaster'         => 'Remaster',
];
$tipo_colores = [
    'dlc'              => 'badge-tipo-dlc',
    'expansion'        => 'badge-tipo-expansion',
    'edicion_especial' => 'badge-tipo-especial',
    'remake'           => 'badge-tipo-remake',
    'remaster'         => 'badge-tipo-remaster',
];
?>

<div class="container mt-4 mb-5 lista-detalle-container">

    <!-- Cabecera -->
    <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
        <?php if ($es_propietario): ?>
            <a href="listas.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Mis listas
            </a>
        <?php else: ?>
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        <?php endif; ?>

        <h1 class="mb-0"><?= htmlspecialchars($lista['nombre']) ?></h1>

        <?php if (!$es_propietario && isset($lista['nombre_usuario'])): ?>
            <span class="small text-muted">
                por <a href="/gamesocial/backend/controladores/perfil_publico.php?id=<?= $lista['id_usuario'] ?>"
                       class="text-purple"><?= htmlspecialchars($lista['nombre_usuario']) ?></a>
            </span>
        <?php endif; ?>

        <span class="badge <?= $lista['es_publica'] ? 'bg-success' : 'bg-dark border border-secondary' ?>">
            <?= $lista['es_publica'] ? '🌐 Pública' : '🔒 Privada' ?>
        </span>
    </div>

    <?php if ($lista['descripcion']): ?>
        <p class="text-muted mb-4"><?= htmlspecialchars($lista['descripcion']) ?></p>
    <?php else: ?>
        <div class="mb-4"></div>
    <?php endif; ?>

    <!-- Juegos en la lista -->
    <h4>Juegos en esta lista (<?= count($juegos_lista) ?>)</h4>

    <?php if (empty($juegos_lista)): ?>
        <div class="alert alert-dark text-center mb-4 py-4">
            Esta lista está vacía.<?= $es_propietario ? ' Añade juegos del catálogo más abajo.' : '' ?>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5 contenedor-catalogo">
            <?php foreach ($juegos_lista as $j): ?>
                <div class="col">
                    <div class="tarjeta-videojuego h-100">
                        <div class="imagen-wrapper">
                            <img src="<?= htmlspecialchars($j['imagen_url']) ?>"
                                 class="imagen-tarjeta"
                                 alt="<?= htmlspecialchars($j['titulo']) ?>">
                        </div>
                        <div class="cuerpo-tarjeta p-3 d-flex flex-column flex-grow-1">
                            <h5 class="titulo-tarjeta mb-2 text-truncate"
                                title="<?= htmlspecialchars($j['titulo']) ?>">
                                <?= htmlspecialchars($j['titulo']) ?>
                            </h5>

                            <!-- Solo badge tipo si no es juego_base -->
                            <?php if (isset($tipo_etiquetas[$j['tipo']])): ?>
                                <div class="mb-3">
                                    <span class="badge-tipo <?= $tipo_colores[$j['tipo']] ?>">
                                        <?= $tipo_etiquetas[$j['tipo']] ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="mb-3"></div>
                            <?php endif; ?>

                            <?php if ($es_propietario): ?>
                                <form method="POST" class="mt-auto">
                                    <input type="hidden" name="accion"        value="quitar_juego">
                                    <input type="hidden" name="id_lista"      value="<?= $lista['id_lista'] ?>">
                                    <input type="hidden" name="id_videojuego" value="<?= $j['id_videojuego'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-times me-1"></i> Quitar
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="/gamesocial/backend/controladores/detalle.php?id=<?= $j['id_videojuego'] ?>"
                                   class="btn-detalle mt-auto w-100 text-center">
                                    Ver detalle
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Añadir del catálogo (solo propietario) -->
    <?php if ($es_propietario): ?>
        <h4>Añadir juegos del catálogo</h4>

        <div class="mb-3">
            <div class="input-group input-busqueda-group" style="max-width:100%;">
                <span class="input-group-text bg-dark border-purple border-end-0">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input type="text"
                       id="filtroCatalogo"
                       class="form-control input-busqueda border-start-0"
                       placeholder="Buscar en el catálogo...">
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 contenedor-catalogo" id="catalogo-agregar">
            <?php foreach ($todos_juegos as $v): ?>
                <div class="col item-catalogo" data-titulo="<?= strtolower(htmlspecialchars($v['titulo'])) ?>">
                    <div class="tarjeta-videojuego h-100 <?= $v['en_lista'] ? 'border-lista-activa' : '' ?>">
                        <div class="imagen-wrapper">
                            <img src="<?= htmlspecialchars($v['imagen_url']) ?>"
                                 class="imagen-tarjeta"
                                 alt="<?= htmlspecialchars($v['titulo']) ?>">
                        </div>
                        <div class="cuerpo-tarjeta p-3 d-flex flex-column flex-grow-1">
                            <h5 class="titulo-tarjeta mb-2 text-truncate"
                                title="<?= htmlspecialchars($v['titulo']) ?>">
                                <?= htmlspecialchars($v['titulo']) ?>
                            </h5>

                            <?php if (isset($tipo_etiquetas[$v['tipo']])): ?>
                                <div class="mb-3">
                                    <span class="badge-tipo <?= $tipo_colores[$v['tipo']] ?>">
                                        <?= $tipo_etiquetas[$v['tipo']] ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="mb-3"></div>
                            <?php endif; ?>

                            <?php if ($v['en_lista']): ?>
                                <div class="mt-auto text-center">
                                    <span class="badge bg-success w-100 py-2">
                                        <i class="fas fa-check me-1"></i> En la lista
                                    </span>
                                </div>
                            <?php else: ?>
                                <form method="POST" class="mt-auto">
                                    <input type="hidden" name="accion"        value="agregar_juego">
                                    <input type="hidden" name="id_lista"      value="<?= $lista['id_lista'] ?>">
                                    <input type="hidden" name="id_videojuego" value="<?= $v['id_videojuego'] ?>">
                                    <input type="hidden" name="redirect"      value="listas.php?ver=<?= $lista['id_lista'] ?>">
                                    <button type="submit" class="btn-detalle w-100">
                                        <i class="fas fa-plus me-1"></i> Añadir
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
        document.getElementById('filtroCatalogo').addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#catalogo-agregar .item-catalogo').forEach(function (el) {
                el.style.display = (q === '' || el.dataset.titulo.includes(q)) ? '' : 'none';
            });
        });
        </script>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
