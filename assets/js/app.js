"use strict";

const formularioAlumno =
    document.getElementById("formularioAlumno");

if (formularioAlumno) {

    const nombre =
        document.getElementById("nombre");

    const apellido =
        document.getElementById("apellido");

    const email =
        document.getElementById("email");

    const patronNombre =
        /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s'-]+$/;

    function validarNombre(campo) {
        const valor = campo.value.trim();

        const esValido =
            valor.length >= 2 &&
            valor.length <= 60 &&
            patronNombre.test(valor);

        campo.setCustomValidity(
            esValido
                ? ""
                : "Ingresá solamente letras."
        );
    }

    function validarEmail() {
        email.value = email.value
            .trim()
            .toLowerCase();
    }

    nombre.addEventListener("input", () => {
        validarNombre(nombre);
    });

    apellido.addEventListener("input", () => {
        validarNombre(apellido);
    });

    email.addEventListener(
        "input",
        validarEmail
    );

    formularioAlumno.addEventListener(
        "submit",
        (evento) => {

            validarNombre(nombre);
            validarNombre(apellido);
            validarEmail();

            if (!formularioAlumno.checkValidity()) {
                evento.preventDefault();
                evento.stopPropagation();
            }

            formularioAlumno.classList.add(
                "was-validated"
            );
        }
    );
}
