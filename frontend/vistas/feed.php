<?php 
/**
 * Vista: feed.php
 * Propósito: Muro social con sistema de hilos anidados y reacciones.
 */
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container mt-4 contenedor-feed mb-3">
    <h2 class="feed-titulo text-center mb-4">Muro de la Comunidad</h2>

    <div class="card-publicar mb-4">
        <form method="POST" action="/gamesocial/backend/controladores/feed.php">
            <textarea name="contenido" 
                      class="gamesocial-input" 
                      rows="3" 
                      placeholder="¿Qué tienes en mente, gamer?" 
                      required></textarea>
            <div class="text-end">
                <button class="btn-detalle">🚀 Publicar</button>
            </div>
        </form>
    </div>

    <?php
    // =========================
    // FUNCIONES AUXILIARES
    // =========================

    function mostrarRespuestas($respuestas, $nivel = 0, $padre_nombre = null){
        foreach($respuestas as $r):
            $nivelClass = 'nivel-' . max(0, min(4, $nivel));
            $hijas = $r['respuestas'] ?? [];
        ?>
        <div class="comentario reply <?= $nivelClass ?>" data-id="<?= $r['id_respuesta'] ?>">
            <div class="cabecera-comentario">
                <div class="usuario"><?= htmlspecialchars($r['nombre_usuario']) ?></div>
                <div class="meta"><?= htmlspecialchars($r['fecha_creacion']) ?></div>
            </div>

            <?php if(!empty($padre_nombre)): ?>
                <div class="en-respuesta">@<?= htmlspecialchars($padre_nombre) ?></div>
            <?php endif; ?>

            <div class="cuerpo-comentario"><?= nl2br(htmlspecialchars($r['contenido'])) ?></div>

            <div class="acciones-comentario">
                <button type="button" class="btn-responder" onclick="mostrarFormulario(<?= $r['id_respuesta'] ?>)">Responder</button>
                <a href="/gamesocial/backend/controladores/like.php?tipo=respuesta&id=<?= $r['id_respuesta'] ?>" class="btn-like">
                    ❤️ <?= $r['likes'] ?? 0 ?>
                </a>
            </div>

            <form method="POST" action="/gamesocial/backend/controladores/feed.php" class="form-respuesta d-none" id="form-<?= $r['id_respuesta'] ?>">
                <textarea name="contenido" class="gamesocial-input mt-2" rows="2" placeholder="Escribe tu respuesta..." required></textarea>
                <input type="hidden" name="id_post" value="<?= $r['id_post'] ?>">
                <input type="hidden" name="id_respuesta_padre" value="<?= $r['id_respuesta'] ?>">
                <button class="btn-detalle btn-sm mt-1">Enviar</button>
            </form>

            <?php if(!empty($hijas)): ?>
                <div class="respuestas">
                    <?php mostrarRespuestas($hijas, $nivel+1, $r['nombre_usuario']); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php
        endforeach;
    }

    function asignarLikesRecursivo(&$respuestas, $modelo_like) {
        foreach ($respuestas as &$r) {
            $r['likes'] = $modelo_like->contar('respuesta', $r['id_respuesta']);
            if (!empty($r['respuestas'])) {
                asignarLikesRecursivo($r['respuestas'], $modelo_like);
            }
        }
    }

    // =========================
    // RENDERIZADO DE POSTS
    // =========================
    foreach($posts as $post):
        $post['likes'] = $modelo_like->contar('post', $post['id_post']);
        if(!empty($post['respuestas'])) {
            asignarLikesRecursivo($post['respuestas'], $modelo_like);
        }
    ?>
        <div class="comentario post">
            <div class="cabecera-comentario">
                <div class="usuario"><?= htmlspecialchars($post['nombre_usuario']) ?></div>
                <div class="meta"><?= htmlspecialchars($post['fecha_creacion']) ?></div>
            </div>
            <div class="cuerpo-comentario"><?= nl2br(htmlspecialchars($post['contenido'])) ?></div>

            <div class="acciones-comentario">
                <button type="button" class="btn-responder" onclick="mostrarFormularioPost(<?= $post['id_post'] ?>)">Comentar</button>
                <a href="/gamesocial/backend/controladores/like.php?tipo=post&id=<?= $post['id_post'] ?>" class="btn-like">
                    ❤️ <?= $post['likes'] ?? 0 ?>
                </a>
            </div>

            <form method="POST" action="/gamesocial/backend/controladores/feed.php" class="form-respuesta d-none" id="form-post-<?= $post['id_post'] ?>">
                <textarea name="contenido" class="gamesocial-input mt-2" rows="2" placeholder="Escribe un comentario..." required></textarea>
                <input type="hidden" name="id_post" value="<?= $post['id_post'] ?>">
                <button class="btn-detalle btn-sm mt-1">Enviar</button>
            </form>

            <?php if(!empty($post['respuestas'])): ?>
                <div class="respuestas">
                    <?php mostrarRespuestas($post['respuestas'], 1, $post['nombre_usuario']); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
function mostrarFormulario(id){
    const f = document.getElementById('form-' + id);
    if(f) f.classList.toggle('d-none');
}
function mostrarFormularioPost(id){
    const f = document.getElementById('form-post-' + id);
    if(f) f.classList.toggle('d-none');
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>