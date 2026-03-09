/**
 * detalle.js
 * Propósito: Gestionar la interactividad de la página de detalle de un videojuego.
 * Proyecto: GameSocial
 *
 * Funcionalidades:
 *   - Botones de estado (pendiente / en_progreso / finalizado)
 *   - Botones de valoración por estrellas (1-5)
 *   - Toggle del formulario de respuesta a comentarios
 */

const form = document.getElementById('estadoForm');

if (form) {
    // Botones de estado: actualizan el campo oculto y envían el formulario
    form.querySelectorAll('[data-estado]').forEach(boton => {
        boton.addEventListener('click', () => {
            form.elements['estado'].value = boton.dataset.estado;
            form.submit();
        });
    });

    // Botones de valoración: actualizan la puntuación y envían el formulario
    form.querySelectorAll('[data-valoracion]').forEach(boton => {
        boton.addEventListener('click', () => {
            form.elements['valoracion'].value = boton.dataset.valoracion;
            form.submit();
        });
    });
}

// Toggle del formulario de respuesta a comentarios
// Usamos delegación de eventos para cubrir comentarios cargados dinámicamente
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-responder')) {
        const formulario = e.target.closest('.comentario').querySelector('.form-respuesta');
        if (formulario) {
            formulario.classList.toggle('d-none');
        }
    }
});
