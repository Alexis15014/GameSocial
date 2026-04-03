/**
 * listas.js
 * Gestiona el interruptor público/privado del modal de creación de lista.
 */
(function () {
    const toggle = document.getElementById('labelToggleModal');
    const input  = document.getElementById('inputEsPublica');
    const texto  = document.getElementById('textoVisibilidad');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
        const esPublica = input.value === '1';
        input.value = esPublica ? '0' : '1';
        toggle.classList.toggle('publica', !esPublica);
        toggle.classList.toggle('privada',  esPublica);
        texto.textContent = esPublica ? '🔒 Privada' : '🌐 Pública';
        texto.className   = esPublica ? 'small text-muted' : 'small text-purple';
    });
})();
