<?php include __DIR__ . '/../../partials/header_admin.php'; ?>

<div class="container mt-4 videojuegos-container">
    <h2 class="text-center">🎮 Gestión de videojuegos</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="" method="GET" class="form-busqueda">
                <div class="input-busqueda-group">
                    <input type="text" name="q" class="input-busqueda" 
                           placeholder="Buscar por título..." 
                           value="<?= htmlspecialchars($busqueda) ?>">
                    
                    <button type="submit" class="btn-busqueda">
                        🔍 Filtrar
                    </button>

                    <?php if (!empty($busqueda)): ?>
                        <a href="videojuegos.php" class="btn-limpiar" title="Limpiar búsqueda">
                            ✖
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="/gamesocial/backend/controladores/admin/videojuego_crear.php" class="btn btn-success">
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($videojuegos)): ?>
                <tr>
                    <td colspan="3" class="text-center py-4">No se encontraron videojuegos con esos criterios.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($videojuegos as $juego): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($juego['titulo']) ?></td>
                        <td>
                            <span class="badge bg-dark border border-secondary text-info">
                                <?= htmlspecialchars($juego['plataforma']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="/gamesocial/backend/controladores/admin/videojuego_editar.php?id=<?= $juego['id_videojuego'] ?>"
                               class="btn btn-sm btn-detalle">
                               ✏ Editar
                            </a>

                            <a href="/gamesocial/backend/controladores/admin/videojuego_eliminar.php?id=<?= $juego['id_videojuego'] ?>"
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