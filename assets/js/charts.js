"use strict";

if (typeof Chart !== "undefined") {
    const estilos = getComputedStyle(document.documentElement);
    const color = nombre => estilos.getPropertyValue(nombre).trim();
    Chart.defaults.color = color("--texto-secundario");
    Chart.defaults.font.family = color("--fuente");

    for (const lienzo of document.querySelectorAll("canvas[data-tipo-grafico]")) {
        const tipo = lienzo.dataset.tipoGrafico;
        const etiquetas = JSON.parse(lienzo.dataset.etiquetas);
        const valores = JSON.parse(lienzo.dataset.valores);
        const circular = tipo === "doughnut";
        const linea = tipo === "line";
        const principal = color("--azul-medio");

        // Chart.js necesita medir un contenedor visible al inicializar.
        lienzo.dataset.graficoListo = "";
        try {
            new Chart(lienzo, {
                type: tipo,
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: circular ? "Usuarios" : "Altas de alumnos",
                        data: valores,
                        backgroundColor: circular
                            ? ["--azul-noche", "--azul-medio", "--serie-cyan", "--serie-pizarra"].map(color)
                            : linea ? color("--rol-docente-fondo") : principal,
                        borderColor: circular ? color("--superficie") : principal,
                        borderWidth: linea ? 2 : 1,
                        borderRadius: tipo === "bar" ? 3 : 0,
                        pointRadius: 3,
                        tension: .3,
                        fill: linea,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: matchMedia("(prefers-reduced-motion: reduce)").matches ? false : undefined,
                    cutout: circular ? "65%" : undefined,
                    plugins: {
                        legend: {
                            display: circular,
                            position: "bottom",
                            labels: { boxWidth: 12, padding: 10 },
                        },
                    },
                    scales: circular ? undefined : {
                        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });

            const datos = lienzo.parentElement.nextElementSibling;
            datos.open = false;
        } catch (error) {
            delete lienzo.dataset.graficoListo;
            console.error("No fue posible dibujar el gráfico.", error);
        }
    }
}
