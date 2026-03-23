<?php 
/**
 * Vista: mis_juegos.php
 */
include __DIR__ . '/../partials/header.php'; 

$estados_etiquetas_mj = [
    'sin_iniciar' => 'Sin iniciar',
    'inacabado'   => 'Inacabado',
    'terminado'   => 'Terminado',
    'completado'  => 'Completado',
    'en_curso'    => 'En curso',
    'abandonado'  => 'Abandonado',
];
$estados_clases_mj = [
    'sin_iniciar' => 'bg-estado-sin_iniciar',
    'inacabado'   => 'bg-estado-inacabado',
    'terminado'   => 'bg-estado-terminado',
    'completado'  => 'bg-estado-completado',
    'en_curso'    => 'bg-estado-en_curso',
    'abandonado'  => 'bg-estado-abandonado',
];
$tipo_etq_mj = [
    'dlc'              => 'DLC',
    'expansion'        => 'Expansión',
    'edicion_especial' => 'Edición especial',
    'remake'           => 'Remake',
    'remaster'         => 'Remaster',
];
$tipo_col_mj = [
    'dlc'              => 'badge-tipo-dlc',
    'expansion'        => 'badge-tipo-expansion',
    'edicion_especial' => 'badge-tipo-especial',
    'remake'           => 'badge-tipo-remake',
    'remaster'         => 'badge-tipo-remaster',
];
?>

<div class="container mt-4 contenedor-catalogo">
    <h1 class="mb-4 catalogo-titulo">Mi Colección</h1>

    <?php if (empty($juegos)): ?>
        <div class="alert alert-dark text-center py-5">
            <p class="mb-3">Parece que tu biblioteca está vacía.</p>
            <a href="catalogo.php" class="btn-detalle">Explorar Catálogo</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($juegos as $juego): ?>
                <div class="col">
                    <div class="tarjeta-videojuego h-100">

                        <!-- Imagen estilo catálogo con badge estado encima -->
                        <div class="imagen-wrapper">
                            <img src="<?= htmlspecialchars($juego['imagen_url']); ?>"
                                 class="imagen-tarjeta"
                                 alt="Portada de <?= htmlspecialchars($juego['titulo']); ?>">
                        </div>

                        <div class="cuerpo-tarjeta p-3 d-flex flex-column flex-grow-1">
                            <h5 class="titulo-tarjeta mb-2 text-truncate"
                                title="<?= htmlspecialchars($juego['titulo']); ?>">
                                <?= htmlspecialchars($juego['titulo']); ?>
                            </h5>

                            <!-- Estado: badge grande y visible, encima de las estrellas -->
                            <?php
                            $est_clave = $juego['estado'] ?? 'sin_iniciar';
                            $est_clase = $estados_clases_mj[$est_clave] ?? 'bg-secondary';
                            $est_label = $estados_etiquetas_mj[$est_clave] ?? ucfirst($est_clave);
                            ?>
                            <div class="mb-2">
                                <span class="badge badge-estado-mj <?= $est_clase ?>">
                                    <?= $est_label ?>
                                </span>
                            </div>

                            <!-- Estrellas de valoración -->
                            <div class="mb-3 text-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="estrella <?= ($juego['valoracion'] >= $i) ? 'activa' : 'inactiva'; ?>">★</span>
                                <?php endfor; ?>
                            </div>

                            <!-- Tipo (solo si no es juego_base) -->
                            <?php
                            $tipo_mj = $juego['tipo'] ?? 'juego_base';
                            if (isset($tipo_etq_mj[$tipo_mj])): ?>
                                <div class="mb-2">
                                    <span class="badge-tipo <?= $tipo_col_mj[$tipo_mj] ?>">
                                        <?= $tipo_etq_mj[$tipo_mj] ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <a href="../../backend/controladores/detalle.php?id=<?= $juego['id_videojuego']; ?>"
                               class="btn-detalle mt-auto w-100 text-center">
                                Ver Detalle
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
