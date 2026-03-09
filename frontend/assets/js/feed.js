/**
 * feed.js
 * Propósito: Gestionar la visibilidad de los formularios de respuesta en el muro social.
 * Proyecto: GameSocial
 *
 * Funcionalidades:
 *   - mostrarFormulario: toggle del formulario de respuesta a una respuesta anidada
 *   - mostrarFormularioPost: toggle del formulario de comentario en un post raíz
 */

/**
 * Muestra u oculta el formulario de respuesta a una respuesta anidada.
 * @param {number|string} idRespuesta - ID de la respuesta sobre la que se responde
 */
function mostrarFormulario(idRespuesta) {
    const formulario = document.getElementById('form-' + idRespuesta);
    if (formulario) {
        formulario.classList.toggle('d-none');
    }
}

/**
 * Muestra u oculta el formulario de comentario en un post raíz.
 * @param {number|string} idPost - ID del post al que se quiere responder
 */
function mostrarFormularioPost(idPost) {
    const formulario = document.getElementById('form-post-' + idPost);
    if (formulario) {
        formulario.classList.toggle('d-none');
    }
}
