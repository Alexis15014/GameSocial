<?php include __DIR__ . '/../../partials/header_admin.php'; ?>

<div class="container mt-4 panel-videojuego mb-4">

    <h2 class="titulo-videojuego"><?= $modo === 'editar' ? '✏ Editar videojuego' : '➕ Nuevo videojuego' ?></h2>

    <form method="POST" class="form-videojuego" enctype="multipart/form-data">

        <!-- Título -->
        <div class="form-group">
            <label class="label-videojuego">Título</label>
            <input type="text" name="titulo" class="input-videojuego"
                   value="<?= $videojuego['titulo'] ?? '' ?>" required>
        </div>

        <!-- Descripción -->
        <div class="form-group">
            <label class="label-videojuego">Descripción</label>
            <textarea name="descripcion" class="textarea-videojuego" rows="5" required><?= $videojuego['descripcion'] ?? '' ?></textarea>
        </div>

        <!-- Plataformas -->
        <div class="form-group">
            <label class="label-videojuego">Plataformas</label>
            <input type="text" name="plataforma" class="input-videojuego"
                   placeholder="PC, PS5, Xbox, Switch"
                   value="<?= $videojuego['plataforma'] ?? '' ?>" required>
        </div>

        <!-- Género -->
        <div class="form-group">
            <label class="label-videojuego">Género</label>
            <input type="text" name="genero" class="input-videojuego"
                   placeholder="RPG, Acción, Aventura, Metroidvania"
                   value="<?= $videojuego['genero'] ?? '' ?>" required>
        </div>

        <!-- Fecha de lanzamiento -->
        <div class="form-group">
            <label class="label-videojuego">Fecha de lanzamiento</label>
            <input type="date" name="fecha_lanzamiento" class="input-videojuego"
                   value="<?= $videojuego['fecha_lanzamiento'] ?? '' ?>" required>
        </div>

        <!-- Desarrolladora -->
        <div class="form-group">
            <label class="label-videojuego">Desarrolladora</label>
            <input type="text" name="desarrolladora" class="input-videojuego"
                   placeholder="Nombre del estudio o compañía"
                   value="<?= $videojuego['desarrolladora'] ?? '' ?>" required>
        </div>

        <!-- Tipo de contenido -->
        <div class="form-group">
            <label class="label-videojuego">Tipo de contenido</label>
            <select name="tipo" class="input-videojuego" required>
                <?php
                $tipos = [
                    'juego_base'      => 'Juego base',
                    'dlc'             => 'DLC',
                    'expansion'       => 'Expansión',
                    'edicion_especial'=> 'Edición especial',
                    'remake'          => 'Remake',
                    'remaster'        => 'Remaster',
                ];
                $tipo_actual = $videojuego['tipo'] ?? 'juego_base';
                foreach ($tipos as $valor => $etiqueta):
                ?>
                    <option value="<?= $valor ?>" <?= $tipo_actual === $valor ? 'selected' : '' ?>>
                        <?= $etiqueta ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="videojuegos.php" class="btn-cancelar">Cancelar</a>
        </div>

    </form>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>
