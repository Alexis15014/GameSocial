<?php
/**
 * Vista: mis_juegos.php
 */
require_once __DIR__ . '/../../backend/config/tipos_videojuego.php';
require_once __DIR__ . '/../../backend/config/estados_juego.php';
include __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4 contenedor-catalogo mb-4">
    <h1 class="mb-4 catalogo-titulo">Mi Colección</h1>

    <?php if (empty($juegos)): ?>
        <div class="alert alert-dark text-center py-5">
            <p class="mb-3">Parece que tu biblioteca está vacía.</p>
            <a href="/gamesocial/catalogo" class="btn-detalle">Explorar Catálogo</a>
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
                            $est_clase = $estados_clases[$est_clave] ?? 'bg-secondary';
                            $est_label = $estados_etiquetas[$est_clave] ?? ucfirst($est_clave);
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

                            <a href="/gamesocial/juego/<?= generarSlug($juego['titulo']); ?>"
                               class="btn-detalle mt-auto">
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
