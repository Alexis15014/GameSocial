<?php include __DIR__ . '/../../partials/header_admin.php'; ?>

<div class="container mt-4 videojuegos-container">
    <h2 class="text-center">🎮 Gestión de videojuegos</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="/gamesocial/admin/videojuegos" method="GET" class="form-busqueda">
                <div class="input-busqueda-group">
                    <input type="text" name="q" class="input-busqueda" 
                           placeholder="Buscar por título..." 
                           value="<?= htmlspecialchars($busqueda) ?>">
                    
                    <button type="submit" class="btn-busqueda">
                        🔍 Filtrar
                    </button>

                    <?php if (!empty($busqueda)): ?>
                        <a href="/gamesocial/admin/videojuegos" class="btn-limpiar" title="Limpiar búsqueda">
                            ✖
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="/gamesocial/admin/videojuego/crear" class="btn btn-success">
            ➕ Nuevo videojuego
        </a>
        <span class="text-light">Resultados: <?= count($videojuegos) ?></span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Plataformas</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($videojuegos)): ?>
                <tr>
                    <td colspan="4" class="text-center py-4">No se encontraron videojuegos con esos criterios.</td>
                </tr>
            <?php else: ?>
                <?php
                $tipo_etq_adm = [
                    'juego_base'       => 'Juego base',
                    'dlc'              => 'DLC',
                    'expansion'        => 'Expansión',
                    'edicion_especial' => 'Edición especial',
                    'remake'           => 'Remake',
                    'remaster'         => 'Remaster',
                ];
                $tipo_col_adm = [
                    'juego_base'       => 'badge-tipo-base',
                    'dlc'              => 'badge-tipo-dlc',
                    'expansion'        => 'badge-tipo-expansion',
                    'edicion_especial' => 'badge-tipo-especial',
                    'remake'           => 'badge-tipo-remake',
                    'remaster'         => 'badge-tipo-remaster',
                ];
                ?>
                <?php foreach ($videojuegos as $juego): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($juego['titulo']) ?></td>
                        <td>
                            <span class="badge bg-dark border border-secondary text-info">
                                <?= htmlspecialchars($juego['plataforma']) ?>
                            </span>
                        </td>
                        <td>
                            <?php $t = $juego['tipo'] ?? 'juego_base'; ?>
                            <span class="badge-tipo <?= $tipo_col_adm[$t] ?? 'badge-tipo-base' ?>">
                                <?= $tipo_etq_adm[$t] ?? ucfirst($t) ?>
                            </span>
                        </td>
                        <td>
                            <a href="/gamesocial/admin/videojuego/editar/<?= generarSlug($juego['titulo']) ?>"
                               class="btn btn-sm btn-detalle">
                               ✏ Editar
                            </a>

                            <a href="/gamesocial/admin/videojuego/eliminar/<?= generarSlug($juego['titulo']) ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Eliminar videojuego?')">
                               🗑 Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>