/**
 * perfil.js
 * Inicializa el gráfico donut de estados de colección en el perfil privado.
 * Los datos se leen desde los atributos data-* del canvas #graficoEstados.
 */
(function () {
    const canvas = document.getElementById('graficoEstados');
    if (!canvas) return;

    const rawLabels  = JSON.parse(canvas.dataset.labels);
    const rawDatos   = JSON.parse(canvas.dataset.datos);
    const rawColores = JSON.parse(canvas.dataset.colores);
    const etiquetas  = JSON.parse(canvas.dataset.etiquetas);

    const labels = [], datos = [], colores = [];
    rawLabels.forEach(function (k, i) {
        if (rawDatos[i] > 0) {
            labels.push(etiquetas[k] || k);
            datos.push(rawDatos[i]);
            colores.push(rawColores[i]);
        }
    });

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: datos,
                backgroundColor: colores,
                borderColor: '#1F1B24',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F1B24',
                    borderColor: '#3E2D50',
                    borderWidth: 1,
                    titleColor: '#FBB040',
                    bodyColor: '#F5F5F5',
                    callbacks: {
                        label: function (ctx) {
                            const total = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                            const pct   = ((ctx.parsed / total) * 100).toFixed(1);
                            return '  ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
})();
