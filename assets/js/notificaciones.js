"use strict";


const botonNotificaciones =
    document.getElementById(
        "botonNotificaciones"
    );


const panelNotificaciones =
    document.getElementById(
        "panelNotificaciones"
    );


const listaNotificaciones =
    document.getElementById(
        "listaNotificaciones"
    );


const contadorNotificaciones =
    document.getElementById(
        "contadorNotificaciones"
    );


let cantidadNoLeidas = 0;



/* ==================================================
   ABRIR / CERRAR PANEL
================================================== */

if (
    botonNotificaciones &&
    panelNotificaciones
) {

    botonNotificaciones
        .addEventListener(
            "click",
            () => {

                panelNotificaciones
                    .classList
                    .toggle("d-none");

            }
        );

}



/* ==================================================
   ACTUALIZAR CONTADOR
================================================== */

function actualizarContador() {

    if (!contadorNotificaciones) {
        return;
    }


    contadorNotificaciones.textContent =
        cantidadNoLeidas;


    if (cantidadNoLeidas > 0) {

        contadorNotificaciones
            .classList
            .remove("d-none");

    } else {

        contadorNotificaciones
            .classList
            .add("d-none");

    }

}



/* ==================================================
   MOSTRAR NOTIFICACIÓN
================================================== */

function mostrarNotificacion(datos) {

    if (!listaNotificaciones) {
        return;
    }


    const vacio =
        listaNotificaciones
            .querySelector(
                ".text-muted"
            );


    if (vacio) {
        vacio.remove();
    }


    const item =
        document.createElement(
            "div"
        );


    item.className =
        "notificacion-item nueva";


    const titulo =
        document.createElement(
            "strong"
        );


    titulo.textContent =
        datos.titulo
        ?? "Nueva notificación";


    const mensaje =
        document.createElement(
            "p"
        );


    mensaje.textContent =
        datos.mensaje
        ?? "";


    const fecha =
        document.createElement(
            "small"
        );


    fecha.textContent =
        "Ahora";


    item.append(
        titulo,
        mensaje,
        fecha
    );


    listaNotificaciones.prepend(
        item
    );


    cantidadNoLeidas++;

    actualizarContador();

}



/* ==================================================
   CONEXIÓN WEBSOCKET
================================================== */

let socket;


function conectarWebSocket() {

    try {

        socket =
            new WebSocket(
                "ws://localhost:8080"
            );


        socket.addEventListener(
            "open",
            () => {

                console.log(
                    "WebSocket conectado"
                );

            }
        );


        socket.addEventListener(
            "message",
            (evento) => {

                try {

                    const datos =
                        JSON.parse(
                            evento.data
                        );


                    mostrarNotificacion(
                        datos
                    );

                } catch (error) {

                    console.error(
                        "Mensaje WebSocket inválido:",
                        error
                    );

                }

            }
        );


        socket.addEventListener(
            "close",
            () => {

                console.log(
                    "WebSocket desconectado"
                );

                setTimeout(
                    conectarWebSocket,
                    3000
                );

            }
        );


        socket.addEventListener(
            "error",
            (error) => {

                console.error(
                    "Error WebSocket:",
                    error
                );

            }
        );

    } catch (error) {

        console.error(
            "No se pudo iniciar WebSocket:",
            error
        );

    }

}


conectarWebSocket();