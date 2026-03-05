<?php 
/**
 * Vista: mis_juegos.php
 */
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container mt-4 mis-juegos-container">
    <h1 class="mb-4">Mi Colección</h1>

    <?php if (empty($juegos)): ?>
        <div class="alert alert-dark text-center py-5">
            <p class="mb-3">Parece que tu biblioteca está vacía.</p>
            <a href="catalogo.php" class="btn-detalle">Explorar Catálogo</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($juegos as $juego): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="tarjeta-videojuego card h-100 shadow-sm">
                        
                        <div class="imagen-wrapper">
                            <img 
                                src="<?= htmlspecialchars($juego['imagen_url']); ?>" 
                                class="imagen-tarjeta"
                                alt="Portada de <?= htmlspecialchars($juego['titulo']); ?>"
                            >
                        </div>

                        <div class="cuerpo-tarjeta card-body d-flex flex-column">
                            <h5 class="titulo-tarjeta mb-3">
                                <?= htmlspecialchars($juego['titulo']); ?>
                            </h5>

                            <div class="contenedor-estado">
                                <span class="badge <?= match($juego['estado']) {
                                    'pendiente'   => 'bg-estado-pendiente',
                                    'en_progreso' => 'bg-estado-progreso',
                                    'finalizado'  => 'bg-estado-finalizado',
                                    default       => 'bg-dark'
                                }; ?>">
                                    <?= ucfirst(str_replace('_',' ', $juego['estado'])); ?>
                                </span>
                            </div>

                            <div class="mb-3 text-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="estrella <?= ($juego['valoracion'] >= $i) ? 'activa' : 'inactiva'; ?>">★</span>
                                <?php endfor; ?>
                            </div>

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