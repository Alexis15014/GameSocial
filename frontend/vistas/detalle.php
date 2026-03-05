<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-4 contenedor-detalle">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <img src="<?= htmlspecialchars($videojuego['imagen_portada_url']) ?>" class="img-fluid rounded shadow imagen-videojuego mb-3" alt="Portada">
            <h1 class="titulo-videojuego mt-4"><?= htmlspecialchars($videojuego['titulo']) ?></h1>
        </div>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-lg-8">
            <div class="card info-videojuego shadow-sm p-3 h-100">
                <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($videojuego['descripcion'])) ?></p>
                <p><strong>Lanzamiento:</strong> <?= htmlspecialchars($videojuego['fecha_lanzamiento']) ?></p>
                <p><strong>Desarrolladora:</strong> <?= htmlspecialchars($videojuego['desarrolladora']) ?></p>
                <p><strong>Plataforma:</strong> <?= htmlspecialchars($videojuego['plataforma']) ?></p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card estado-valoracion shadow-sm p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-3 text-center">Mi estado</h5>
                    <form method="POST" id="estadoForm">
                        <input type="hidden" id="estado" name="estado" value="<?= $estado_actual['estado'] ?? '' ?>">
                        <input type="hidden" id="valoracion" name="valoracion" value="<?= $valoracion_usuario ?? 0 ?>">
                        <div class="d-grid gap-2 mb-3">
                            <?php foreach (['pendiente','en_progreso','finalizado'] as $e): ?>
                                <button type="button" class="btn btn-estado <?= ($estado_actual['estado'] ?? '') === $e ? 'activo' : 'inactivo' ?>" data-estado="<?= $e ?>">
                                    <?= ucfirst(str_replace('_',' ',$e)) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mb-3">
                            <?php for($i=1;$i<=5;$i++): ?>
                                <button type="button" class="btn <?= $valoracion_usuario >= $i ? 'btn-warning' : 'btn-outline-warning' ?> btn-sm" data-valoracion="<?= $i ?>">★</button>
                            <?php endfor; ?>
                        </div>
                    </form>
                </div>
                <p class="text-center mb-0"><strong>Media global:</strong><br><?= round($media_valoracion,1) ?> / 5</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <h4>Comentarios</h4>
            <?php
            require_once __DIR__ . '/../../backend/modelos/Like.php';
            $modelo_like = new Like($conexion);

            function asignarLikesRecursivo(&$comentarios, $modelo_like){
                foreach($comentarios as &$c){
                    $c['likes'] = $modelo_like->contar('comentario', $c['id_comentario']);
                    if(!empty($c['respuestas'])){ asignarLikesRecursivo($c['respuestas'], $modelo_like); }
                }
            }
            if(!empty($comentarios)){ asignarLikesRecursivo($comentarios, $modelo_like); }

            function renderComentarios($comentarios, $nivel = 0, $padre_nombre = null){
                foreach($comentarios as $c):
                    $nivelClass = 'nivel-' . max(0, min(2, $nivel)); ?>
                    <div class="comentario <?= $nivelClass ?>" data-id="<?= $c['id_comentario'] ?>">
                        <div class="cabecera-comentario">
                            <div class="usuario"><?= htmlspecialchars($c['nombre_usuario']) ?></div>
                            <div class="meta"><?= htmlspecialchars($c['fecha_comentario']) ?></div>
                        </div>
                        <?php if(!empty($padre_nombre)): ?>
                            <div class="en-respuesta">En respuesta a <?= htmlspecialchars($padre_nombre) ?></div>
                        <?php endif; ?>
                        <div class="cuerpo-comentario"><?= nl2br(htmlspecialchars($c['contenido'])) ?></div>
                        <div class="acciones-comentario">
                            <button type="button" class="btn-responder">Responder</button>
                            <a href="/gamesocial/backend/controladores/like.php?tipo=comentario&id=<?= $c['id_comentario'] ?>" class="btn btn-sm btn-outline-danger">
                                ❤️ <?= $c['likes'] ?? 0 ?>
                            </a>
                        </div>
                        <form method="POST" class="form-respuesta d-none">
                            <textarea name="contenido" class="form-control mt-2" rows="2" placeholder="Responder..." required></textarea>
                            <input type="hidden" name="id_comentario_padre" value="<?= $c['id_comentario'] ?>">
                            <button class="btn-detalle mt-2">Enviar</button>
                        </form>
                        <?php if(!empty($c['respuestas'])): ?>
                            <div class="respuestas"><?php renderComentarios($c['respuestas'], $nivel+1, $c['nombre_usuario']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
            }
            ?>

            <?php if(!empty($comentarios)): ?>
                <div id="comentarios-lista"><?php renderComentarios($comentarios); ?></div>
            <?php else: ?>
                <p style="color:#F5F5F5;">Sé el primero en comentar.</p>
            <?php endif; ?>

            <h5 class="mt-4">Escribir un comentario</h5>
            <form method="POST" class="mt-2">
                <textarea name="contenido" class="gamesocial-input" rows="4" placeholder="¿Qué te pareció el juego?" required></textarea>
                <button class="btn-detalle mt-2">Publicar</button>
            </form>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('estadoForm');
if(form){
    form.querySelectorAll('[data-estado]').forEach(b => b.addEventListener('click', () => {
        form.elements['estado'].value = b.dataset.estado;
        form.submit();
    }));
    form.querySelectorAll('[data-valoracion]').forEach(b => b.addEventListener('click', () => {
        form.elements['valoracion'].value = b.dataset.valoracion;
        form.submit();
    }));
}
document.addEventListener('click', (e) => {
    if(e.target.classList.contains('btn-responder')){
        const f = e.target.closest('.comentario').querySelector('.form-respuesta');
        if(f) f.classList.toggle('d-none');
    }
});
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>