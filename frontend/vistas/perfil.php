<?php
/**
 * Vista: perfil.php
 * Propósito: Gestión de cuenta, biografía, estadísticas de colección, listas y logros.
 */
require_once __DIR__ . '/../../backend/config/estados_juego.php';
include __DIR__ . '/../partials/header.php';

$total_juegos = array_sum($stats_estados);
?>

<div class="container mt-4 perfil-privado-container">
    <h1 class="text-center mb-4">Mi Perfil</h1>

    <!-- Avatar -->    <div class="text-center mb-5">
        <div class="avatar-wrapper mb-3">
            <img 
                src="<?= !empty($usuario['foto_perfil']) ? '/gamesocial/' . htmlspecialchars($usuario['foto_perfil']) : '/gamesocial/frontend/assets/img/gamesocial.png'; ?>"
                class="img-perfil-circulo shadow-lg"
                alt="Avatar de <?= htmlspecialchars($usuario['nombre_usuario']); ?>"
            >
        </div>

        <?php if (!empty($_GET['error_avatar'])): ?>
            <div class="alert alert-danger text-center mb-3">
                ⚠️ <?= htmlspecialchars(urldecode($_GET['error_avatar'])) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="d-flex justify-content-center flex-column flex-sm-row align-items-center gap-2">
            <input type="file" name="avatar" accept="image/*" class="gamesocial-input archivo-avatar" required>
            <button type="submit" class="btn-detalle btn-archivo-avatar">Actualizar Avatar</button>
        </form>
    </div>

    <!-- Info cuenta -->
    <div class="info-usuario-card mb-4">
        <h3 class="mb-3"><?= htmlspecialchars($usuario['nombre_usuario']); ?></h3>
        <div class="detalles-cuenta text-light">
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Rol:</strong> <span class="badge bg-purple"><?= htmlspecialchars($usuario['rol']); ?></span></p>
            <p><strong>Registro:</strong> <?= htmlspecialchars($usuario['fecha_registro']); ?></p>
        </div>
    </div>

    <!-- Biografía -->
    <form method="POST" class="mb-5 d-flex flex-column gap-3">
        <h4 class="text-light">Sobre mí</h4>
        <textarea name="biografia" 
                  class="gamesocial-input biografia-textarea" 
                  rows="4" 
                  placeholder="Cuéntale a la comunidad quién eres..."><?= htmlspecialchars($usuario['biografia']); ?></textarea>
        <button type="submit" class="btn-biografia align-self-start">Guardar Cambios</button>
    </form>

    <!-- ========================
         ESTADÍSTICAS DE COLECCIÓN
         ======================== -->
    <h4 class="mb-4 text-light">📊 Estadísticas de Colección</h4>

    <?php if ($total_juegos > 0): ?>
        <div class="row g-4 align-items-center mb-5">
            <div class="col-12 col-md-5 text-center">
                <canvas id="graficoEstados" style="max-height:260px;"
                    data-labels='<?= json_encode(array_keys($stats_estados)) ?>'
                    data-datos='<?= json_encode(array_values($stats_estados)) ?>'
                    data-colores='<?= json_encode(array_values($estados_colores_chart)) ?>'
                    data-etiquetas='<?= json_encode($estados_etiquetas) ?>'
                ></canvas>
                <p class="text-muted small mt-3 mb-0">
                    <i class="fas fa-gamepad me-1"></i>
                    <?= $total_juegos ?> juego<?= $total_juegos != 1 ? 's' : '' ?> en tu biblioteca
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
        <script src="/gamesocial/frontend/assets/js/perfil.js"></script>

    <?php else: ?>
        <div class="alert alert-dark text-center mb-5">
            Aún no tienes juegos en tu biblioteca. 
            <a href="/gamesocial/catalogo" class="text-purple">¡Explora el catálogo!</a>
        </div>
    <?php endif; ?>

    <!-- ========================
         MIS LISTAS
         ======================== -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="text-light mb-0">📋 Mis Listas</h4>
        <a href="/gamesocial/listas" class="btn-detalle btn-sm">
            <i class="fas fa-plus me-1"></i> Nueva lista
        </a>
    </div>

    <?php if (!empty($listas_perfil)): ?>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-5">
            <?php foreach ($listas_perfil as $lista): ?>
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
                            <div class="overlay-visibilidad-lista">
                                <span class="badge <?= $lista['es_publica'] ? 'bg-success' : 'bg-dark border border-secondary' ?>">
                                    <?= $lista['es_publica'] ? '🌐 Pública' : '🔒 Privada' ?>
                                </span>
                            </div>
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

                            <div class="d-flex align-items-center gap-2 mb-3">
                                <form method="POST" action="/gamesocial/listas" class="d-inline">
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

                            <div class="d-flex gap-2 mt-auto">
                                <a href="/gamesocial/lista/<?= $lista['id_lista'] ?>-<?= generarSlug($lista['nombre']) ?>" class="btn-detalle flex-grow-1 text-center">
                                    Ver lista
                                </a>
                                <form method="POST" action="/gamesocial/listas" onsubmit="return confirm('¿Eliminar esta lista permanentemente?')">
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
    <?php else: ?>
        <div class="alert alert-dark text-center mb-5">
            Aún no tienes listas. <a href="/gamesocial/listas" class="text-purple">¡Crea tu primera lista!</a>
        </div>
    <?php endif; ?>

    <!-- Logros -->
    <h4 class="mb-4 text-light">🏆 Logros Obtenidos</h4>
    <?php if (!empty($logros)): ?>
        <div class="row g-4">
            <?php foreach ($logros as $logro): ?>
                <div class="col-6 col-md-4">
                    <div class="tarjeta-logro shadow-sm h-100">
                        <div class="icono-logro">🏅</div>
                        <strong class="nombre-logro"><?= htmlspecialchars($logro['nombre']); ?></strong>
                        <div class="descripcion-logro small"><?= htmlspecialchars($logro['descripcion']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-dark text-center">Aún no has desbloqueado ningún logro. ¡Sigue participando!</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
