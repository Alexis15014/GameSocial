<?php 
/**
 * Vista: registro.php
 * Propósito: Interfaz de usuario para la creación de cuentas.
 */

include __DIR__ . '/../partials/header.php'; 
?>

<div class="container gamesocial-registro-container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5">
            <div class="card gamesocial-card border-0">
                <div class="card-body p-4">
                    <h2 class="gamesocial-title text-center mb-4">🎮 Registro</h2>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger gamesocial-alert" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php elseif (!empty($success)): ?>
                        <div class="alert alert-success gamesocial-alert" role="alert">
                            <?= htmlspecialchars($success) ?>
                            <div class="mt-2">
                                <a href="/gamesocial/login" class="btn btn-sm btn-outline-light">Ir al Login</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="gamesocial-form">
                        
                        <div class="mb-3">
                            <label for="nombre" class="text-white form-label gamesocial-label">Nombre de usuario</label>
                            <input type="text" 
                                   class="gamesocial-input" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="text-white form-label gamesocial-label">Correo electrónico</label>
                            <input type="email" 
                                   class="gamesocial-input" 
                                   id="email" 
                                   name="email" 
                                   value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="text-white form-label gamesocial-label">Contraseña</label>
                            <input type="password" 
                                   class="gamesocial-input" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Mínimo 6 caracteres"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="text-white form-label gamesocial-label">Confirmar contraseña</label>
                            <input type="password" 
                                   class="gamesocial-input" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>

                        <button type="submit" class="gamesocial-btn w-100 mb-3">
                            ➕ Registrarse
                        </button>

                        <p class="text-center text-light">
                            ¿Ya tienes cuenta? 
                            <a href="/gamesocial/login" class="gamesocial-link">Iniciar sesión</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>