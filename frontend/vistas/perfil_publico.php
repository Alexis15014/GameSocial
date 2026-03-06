<?php 
/**
 * Vista: perfil_publico.php
 * Propósito: Visualización de perfiles de otros usuarios y sistema de follow.
 */
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container mt-4 perfil-container">
    <div class="text-center mb-4">
        <h3 class="gamesocial-perfil-title"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h3>
        
        <p class="text-light">
            <span class="stat-highlight"><strong><?= $seguidores ?></strong></span> seguidores ·
            <span class="stat-highlight"><strong><?= $seguidos ?></strong></span> siguiendo
        </p>

        <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != $usuario['id_usuario']): ?>
            <div class="mt-3">
                <a href="/gamesocial/backend/controladores/follow.php?id=<?= $usuario['id_usuario'] ?>"
                   class="btn-detalle <?= $esta_siguiendo ? 'btn-unfollow' : '' ?>">
                    <?= $esta_siguiendo ? 'Dejar de seguir' : 'Seguir a este Gamer' ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <hr class="border-secondary my-4">

    <h5 class="section-subtitle mb-4">🏆 Vitrina de Logros</h5>

    <?php if (!empty($logros)): ?>
        <div class="row g-3">
            <?php foreach ($logros as $logro): ?>
                <div class="col-6 col-md-4">
                    <div class="tarjeta-logro text-center p-3 h-100">
                        <div class="icono-logro">🏅</div>
                        <div class="nombre-logro"><?= htmlspecialchars($logro['nombre']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-dark text-center">Este usuario aún está en nivel 1. No tiene logros registrados.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>