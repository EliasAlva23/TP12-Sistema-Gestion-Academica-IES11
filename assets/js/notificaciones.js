"use strict";

const campana = document.getElementById("campanaAvisos");

if (campana && ["localhost", "127.0.0.1"].includes(location.hostname) && location.protocol === "http:") {
    const lista = document.getElementById("listaNotificaciones");
    const contador = document.getElementById("contadorNotificaciones");

    async function actualizarAvisos() {
        try {
            const respuesta = await fetch(campana.dataset.avisosUrl, { credentials: "same-origin" });
            if (!respuesta.ok) return;

            const avisos = await respuesta.json();
            contador.textContent = avisos.length > 5 ? "5+" : String(avisos.length);
            contador.hidden = avisos.length === 0;
            lista.replaceChildren();

            if (avisos.length === 0) {
                lista.textContent = "Sin notificaciones nuevas.";
            }

            for (const aviso of avisos.slice(0, 5)) {
                const articulo = document.createElement("div");
                articulo.className = "notificacion-item";
                const titulo = document.createElement("strong");
                const mensaje = document.createElement("p");
                titulo.textContent = aviso.titulo;
                mensaje.textContent = aviso.mensaje;
                articulo.append(titulo, mensaje);
                lista.append(articulo);
            }
        } catch {
            // El historial sigue disponible por HTTP.
        }
    }

    function conectarAvisos() {
        const socket = new WebSocket(`ws://${location.hostname}:8080`);
        socket.addEventListener("open", actualizarAvisos);
        socket.addEventListener("message", (evento) => {
            if (evento.data === "actualizar") actualizarAvisos();
        });
        socket.addEventListener("close", () => setTimeout(conectarAvisos, 15000));
    }

    conectarAvisos();
}
