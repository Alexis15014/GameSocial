<?php include __DIR__ . '/../../partials/header_admin.php'; ?>

<div class="container mt-4 panel-admin-container">
    
    <h1 class="titulo-panel text-center">🛠️ Panel de administrador</h1>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3 flex-wrap">
        <h3 class="subtitulo-panel mb-0">👤 Usuarios</h3>
        
        <form action="/gamesocial/admin" method="GET" class="form-busqueda">
            <div class="input-busqueda-group">
                <?php $val_user = $busqueda_user ?? ''; ?>
                <input type="text" name="q" class="input-busqueda" 
                       placeholder="Buscar usuario..." 
                       value="<?= htmlspecialchars($val_user) ?>">
                
                <button type="submit" class="btn-busqueda">🔍</button>

                <?php if (!empty($val_user)): ?>
                    <a href="/gamesocial/admin" class="btn-limpiar" title="Limpiar búsqueda">✖</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="usuarios-grid">
        <?php if (empty($usuarios)): ?>
            <p class="text-light">No se encontraron usuarios.</p>
        <?php else: ?>
            <?php foreach ($usuarios as $u): ?>
            <div class="usuario-card card shadow-sm p-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="avatar-usuario-wrapper">
                        <?php 
                            // ARREGLO LÍNEA 33: Validación de imagen
                            $foto = (!empty($u['foto_perfil'])) ? $u['foto_perfil'] : 'frontend/assets/img/gamesocial.png';
                            $ruta_img = (strpos($foto, 'assets') !== false) ? '/gamesocial/' . $foto : '/gamesocial/' . $foto;
                        ?>
                        <img src="<?= htmlspecialchars($ruta_img) ?>" 
                             alt="<?= htmlspecialchars($u['nombre_usuario'] ?? 'Usuario') ?>"
                             class="avatar-usuario rounded-circle">
                    </div>
                    <div>
                        <h5 class="nombre-usuario mb-0"><?= htmlspecialchars($u['nombre_usuario'] ?? 'N/A') ?></h5>
                        <small class="email-usuario"><?= htmlspecialchars($u['email'] ?? '') ?></small><br>
                        <span class="rol-usuario"><?= htmlspecialchars($u['rol'] ?? 'usuario') ?></span>
                    </div>
                </div>
                <div class="acciones-card mt-2 text-center">
                    <a href="?accion=rol&id=<?= $u['id_usuario'] ?? 0 ?>" class="btn btn-sm btn-cambiar-rol">Cambiar rol</a>
                    <a href="?accion=eliminar_usuario&id=<?= $u['id_usuario'] ?? 0 ?>" 
                       class="btn btn-sm btn-eliminar"
                       onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-3 flex-wrap">
        <h3 class="subtitulo-panel mb-0">📝 Posts</h3>
        
        <form action="/gamesocial/admin" method="GET" class="form-busqueda">
            <div class="input-busqueda-group">
                <?php $val_post = $busqueda_post ?? ''; ?>
                <input type="text" name="qp" class="input-busqueda" 
                       placeholder="Buscar contenido..." 
                       value="<?= htmlspecialchars($val_post) ?>">
                
                <button type="submit" class="btn-busqueda">🔍</button>

                <?php if (!empty($val_post)): ?>
                    <a href="/gamesocial/admin" class="btn-limpiar" title="Limpiar búsqueda">✖</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="posts-grid">
        <?php if (empty($posts)): ?>
            <p class="text-light">No hay posts que mostrar.</p>
        <?php else: ?>
            <?php foreach ($posts as $p): ?>
            <div class="post-card card shadow-sm p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <?php 
                        $foto_p = (!empty($p['foto_perfil'])) ? $p['foto_perfil'] : 'frontend/assets/img/gamesocial.png';
                    ?>
                    <img src="<?= htmlspecialchars('/gamesocial/' . $foto_p) ?>" 
                         alt="Avatar" 
                         class="avatar-post rounded-circle" style="width: 40px; height: 40px;">
                    <div class="post-autor fw-bold"><?= htmlspecialchars($p['nombre_usuario'] ?? 'Anónimo') ?></div>
                </div>

                <div class="post-contenido p-2 mb-3">
                    <?= nl2br(htmlspecialchars($p['contenido'] ?? '')) ?>
                </div>

                <a href="?accion=eliminar_post&id=<?= $p['id_post'] ?? 0 ?>" 
                   class="btn btn-sm btn-eliminar w-100"
                   onclick="return confirm('¿Eliminar post?')">Eliminar</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>