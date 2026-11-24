"use strict";


const colores = {

    azul: "#456fbb",

    celeste: "#36b5df",

    profundo: "#2c374e",

    verde: "#2f9e77",

    naranja: "#e0a13a"

};


Chart.defaults.font.family =
    '"Segoe UI", Arial, sans-serif';

Chart.defaults.color =
    "#657086";


/* ==================================================
   ALTAS DE ALUMNOS
================================================== */

const lienzoAltas =
    document.getElementById(
        "altasChart"
    );


if (lienzoAltas) {

    new Chart(
        lienzoAltas,
        {

            type: "line",

            data: {

                labels:
                    datosGraficos
                        .altas
                        .etiquetas,

                datasets: [

                    {

                        label:
                            "Alumnos",

                        data:
                            datosGraficos
                                .altas
                                .valores,

                        borderColor:
                            colores.azul,

                        backgroundColor:
                            "rgba(69,111,187,0.15)",

                        pointBackgroundColor:
                            colores.azul,

                        tension: 0.3,

                        fill: true

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio:
                    false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            precision: 0

                        }

                    }

                }

            }

        }

    );

}


/* ==================================================
   USUARIOS POR ROL
================================================== */

const lienzoRoles =
    document.getElementById(
        "rolesChart"
    );


if (lienzoRoles) {

    new Chart(
        lienzoRoles,
        {

            type: "doughnut",

            data: {

                labels:
                    datosGraficos
                        .roles
                        .etiquetas,

                datasets: [

                    {

                        data:
                            datosGraficos
                                .roles
                                .valores,

                        backgroundColor: [

                            colores.profundo,

                            colores.azul,

                            colores.celeste,

                            colores.naranja

                        ],

                        borderColor:
                            "#ffffff",

                        borderWidth:
                            2

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio:
                    false,

                plugins: {

                    legend: {

                        position:
                            "bottom"

                    }

                }

            }

        }

    );

}