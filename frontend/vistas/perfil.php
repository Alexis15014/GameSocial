<?php 
/**
 * Vista: perfil.php
 * Propósito: Gestión de cuenta, biografía y visualización de logros.
 */
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container mt-4 perfil-privado-container">
    <h1 class="text-center mb-4">Mi Perfil</h1>

    <div class="text-center mb-5">
        <div class="avatar-wrapper mb-3">
            <img 
                src="<?= $usuario['foto_perfil'] ? '../../' . htmlspecialchars($usuario['foto_perfil']) : '../../frontend/assets/img/gamesocial.png'; ?>"
                class="img-perfil-circulo shadow-lg"
                alt="Avatar de <?= htmlspecialchars($usuario['nombre_usuario']); ?>"
            >
        </div>

        <form method="POST" enctype="multipart/form-data" class="d-flex justify-content-center flex-column flex-sm-row align-items-center gap-2">
            <input type="file" name="avatar" accept="image/*" class="gamesocial-input archivo-avatar" required>
            <button type="submit" class="btn-detalle btn-archivo-avatar">Actualizar Avatar</button>
        </form>
    </div>

    <div class="info-usuario-card mb-4">
        <h3 class="mb-3"><?= htmlspecialchars($usuario['nombre_usuario']); ?></h3>
        <div class="detalles-cuenta text-light">
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Rol:</strong> <span class="badge bg-purple"><?= htmlspecialchars($usuario['rol']); ?></span></p>
            <p><strong>Registro:</strong> <?= htmlspecialchars($usuario['fecha_registro']); ?></p>
        </div>
    </div>

    <form method="POST" class="mb-5 d-flex flex-column gap-3">
        <h4 class="text-light">Sobre mí</h4>
        <textarea name="biografia" 
                  class="gamesocial-input biografia-textarea" 
                  rows="4" 
                  placeholder="Cuéntale a la comunidad quién eres..."><?= htmlspecialchars($usuario['biografia']); ?></textarea>
        <button type="submit" class="btn-biografia align-self-start">Guardar Cambios</button>
    </form>

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