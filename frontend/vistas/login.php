<?php 
/**
 * Vista: login.php
 * Propósito: Formulario de acceso para usuarios registrados.
 * Integra la funcionalidad de "Recordarme" mediante cookies.
 */
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container login-container my-5">
    <div class="login-card">
        <h3 class="gamesocial-login-title">Iniciar sesión</h3>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger gamesocial-alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="email" 
                       name="email" 
                       class="gamesocial-input" 
                       placeholder="Email" 
                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                       required>
            </div>

            <div class="mb-3">
                <input type="password" 
                       name="password" 
                       class="gamesocial-input" 
                       placeholder="Contraseña" 
                       required>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" 
                       name="recordarme" 
                       class="form-check-input" 
                       id="recordarme">
                <label class="form-check-label" for="recordarme">Recordarme</label>
            </div>

            <button class="btn-detalle mt-2 mb-3 w-100" type="submit">Entrar</button>
            
            <p class="text-center text-light">
                ¿No tienes cuenta? 
                <a href="/gamesocial/registro" class="gamesocial-link">Registrarse</a>
            </p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>