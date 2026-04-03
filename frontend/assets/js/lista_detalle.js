/**
 * lista_detalle.js
 * Filtro de búsqueda en tiempo real sobre el catálogo de juegos para añadir a una lista.
 */
(function () {
    const input = document.getElementById('filtroCatalogo');
    if (!input) return;

    input.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('#catalogo-agregar .item-catalogo').forEach(function (el) {
            el.style.display = (q === '' || el.dataset.titulo.includes(q)) ? '' : 'none';
        });
    });
})();
