<?php
/**
 * Vista: listas.php
 * Propósito: Mostrar y gestionar las listas de videojuegos del usuario.
 */
include __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4 mb-5 contenedor-catalogo">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="catalogo-titulo">Mis Listas</h1>
        <button class="btn-detalle" data-bs-toggle="modal" data-bs-target="#modalCrearLista">
            <i class="fas fa-plus me-1"></i> Nueva lista
        </button>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-dark text-center mb-4">
            <?= $_GET['ok'] === 'creada' ? '✅ Lista creada correctamente.' : '🗑️ Lista eliminada.' ?>
        </div>
    <?php endif; ?>

    <?php if (empty($listas)): ?>
        <div class="alert alert-dark text-center py-5">
            <p class="mb-3">Aún no tienes ninguna lista. ¡Crea tu primera!</p>
            <button class="btn-detalle" data-bs-toggle="modal" data-bs-target="#modalCrearLista">
                <i class="fas fa-plus me-1"></i> Crear lista
            </button>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($listas as $lista): ?>
                <div class="col">
                    <div class="tarjeta-videojuego h-100">

                        <!-- Portada: imagen del primer juego o placeholder -->
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

                            <!-- Badge visibilidad sobre la imagen -->
                            <div class="overlay-visibilidad-lista">
                                <span class="badge <?= $lista['es_publica'] ? 'bg-success' : 'bg-dark border border-secondary' ?>">
                                    <?= $lista['es_publica'] ? '🌐 Pública' : '🔒 Privada' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Cuerpo -->
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

                            <!-- Interruptor público/privado -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion"   value="toggle_visibilidad">
                                    <input type="hidden" name="id_lista" value="<?= $lista['id_lista'] ?>">
                                    <button type="submit"
                                            class="interruptor-visibilidad <?= $lista['es_publica'] ? 'publica' : 'privada' ?>"
                                            title="<?= $lista['es_publica'] ? 'Lista pública — clic para hacerla privada' : 'Lista privada — clic para hacerla pública' ?>">
                                        <span class="interruptor-dot"></span>
                                    </button>
                                </form>
                                <span class="small <?= $lista['es_publica'] ? 'text-purple' : 'text-muted' ?>">
                                    <?= $lista['es_publica'] ? 'Pública' : 'Privada' ?>
                                </span>
                            </div>

                            <!-- Acciones -->
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/gamesocial/lista/<?= $lista['id_lista'] ?>-<?= generarSlug($lista['nombre']) ?>" class="btn-detalle flex-grow-1 text-center">
                                    Ver lista
                                </a>
                                <form method="POST" onsubmit="return confirm('¿Eliminar esta lista permanentemente?')">
                                    <input type="hidden" name="accion"   value="eliminar">
                                    <input type="hidden" name="id_lista" value="<?= $lista['id_lista'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm h-100" title="Eliminar lista">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Crear lista -->
<div class="modal fade" id="modalCrearLista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color:#1F1B24; border:1px solid #3E2D50; color:#F5F5F5;">
            <div class="modal-header" style="border-bottom:1px solid #3E2D50;">
                <h5 class="modal-title" style="font-family:'Orbitron',sans-serif; color:#6E48AA;">
                    <i class="fas fa-plus me-2"></i>Nueva lista
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label text-light">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" maxlength="100" class="gamesocial-input w-100"
                               required placeholder="Ej: Juegos del verano" style="margin-bottom:0;">
                    </div>
                    <div>
                        <label class="form-label text-light">Descripción <span class="text-muted">(opcional)</span></label>
                        <textarea name="descripcion" rows="3" class="gamesocial-input w-100"
                                  placeholder="Descripción de la lista..." style="margin-bottom:0;"></textarea>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <label class="text-light mb-0">Visibilidad</label>
                        <button type="button" class="interruptor-visibilidad publica" id="labelToggleModal">
                            <span class="interruptor-dot"></span>
                        </button>
                        <input type="hidden" name="es_publica" id="inputEsPublica" value="1">
                        <span class="small text-purple" id="textoVisibilidad">🌐 Pública</span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #3E2D50;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-detalle">Crear lista</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/gamesocial/frontend/assets/js/listas.js"></script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
