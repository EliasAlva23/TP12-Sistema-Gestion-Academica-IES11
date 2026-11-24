"use strict";

const botonMenu = document.getElementById("botonMenu");
const sidebar = document.getElementById("sidebar");
const fondo = document.getElementById("sidebarFondo");

function cerrarMenu() {
    sidebar.classList.remove("abierto");
    botonMenu.setAttribute("aria-expanded", "false");
}

if (botonMenu && sidebar) {

    botonMenu.setAttribute("aria-expanded", "false");

    // Abre o cierra el menú con el botón ☰
    botonMenu.addEventListener("click", () => {
        const estaAbierto = sidebar.classList.toggle("abierto");
        botonMenu.setAttribute("aria-expanded", String(estaAbierto));
    });

    // Cierra el menú al tocar el fondo oscuro
    if (fondo) {
        fondo.addEventListener("click", cerrarMenu);
    }

    // Cierra el menú con la tecla Escape
    document.addEventListener("keydown", (evento) => {
        if (evento.key === "Escape") {
            cerrarMenu();
        }
    });
}
