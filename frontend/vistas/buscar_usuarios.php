<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-4 contenedor-busqueda">
    <h3 class="titulo-busqueda mb-4">🔍 Buscar Exploradores</h3>

    <form method="GET" action="/gamesocial/buscar-usuarios" class="mb-4">
        <div class="input-group search-box-container">
            <span class="input-group-text bg-dark border-purple text-white">
                <i class="fas fa-search"></i>
            </span>
            <input 
                type="text" 
                name="q" 
                class="form-control input-busqueda" 
                placeholder="Escribe un nombre de usuario..." 
                value="<?= isset($termino) ? htmlspecialchars($termino) : '' ?>" 
                autofocus
            >
        </div>
    </form>

    <?php if (!empty($resultados)): ?>
        <div class="lista-resultados">
            <?php foreach ($resultados as $u): ?>
                <div class="item-usuario-card d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-wrapper">
                        <img 
                            src="<?= htmlspecialchars($u['avatar_url']) ?>" 
                            class="avatar-usuario rounded-circle" 
                            alt="Avatar"
                        >
                    </div>

                    <div class="info-usuario flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <a class="nombre-usuario-link" href="/gamesocial/usuario/<?= urlencode($u['nombre_usuario']) ?>">
                                <?= htmlspecialchars($u['nombre_usuario']) ?>
                            </a>
                            <?php if (!empty($u['rol']) && $u['rol'] === 'admin'): ?>
                                <span class="badge badge-admin">ADMIN</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="biografia-usuario mb-0">
                            <?= !empty($u['biografia']) ? htmlspecialchars($u['biografia']) : 'Sin biografía disponible.' ?>
                        </p>
                    </div>

                    <div class="acciones-busqueda">
                        <a href="/gamesocial/usuario/<?= urlencode($u['nombre_usuario']) ?>" class="btn btn-sm btn-outline-purple">
                            Ver Perfil
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif (isset($termino) && $termino !== ''): ?>
        <div class="alert-busqueda-vacia text-center">
            <p class="mb-0">No se han encontrado jugadores con ese nombre.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>