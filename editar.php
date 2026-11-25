<?php

$seccionRequerida = "crud_alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";

/* Valida el identificador recibido */
$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
}

/* Busca el alumno mediante una consulta preparada */
$consulta = $conexion->prepare(
    "SELECT id, nombre, apellido, email
     FROM alumnos
     WHERE id = :id"
);

$consulta->execute([
    ":id" => $id
]);

$alumno = $consulta->fetch();

if (!$alumno) {
    redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
}

/* Mensajes enviados por actualizar.php */
$error = (is_string($_GET["error"] ?? null) ? $_GET["error"] : "");

$mensajesError = [
    "campos" =>
        "Completá todos los campos obligatorios.",

    "longitud" =>
        "El nombre y el apellido deben tener entre 2 y 60 caracteres.",

    "nombre" =>
        "El nombre y el apellido solamente pueden contener letras.",

    "email" =>
        "Ingresá un correo electrónico válido.",

    "duplicado" =>
        "Ya existe otro alumno registrado con ese correo electrónico.",

    "servidor" =>
        "No fue posible actualizar los datos. Intentá nuevamente."
];

$mensajeError =
    $mensajesError[$error] ?? "";

$titulo = "Editar alumno";
$pagina = "alumnos";
require __DIR__ . "/includes/header.php";
?>


    <main id="contenido-principal" tabindex="-1" class="pagina-contenido pagina-formulario">
    <header class="encabezado-pagina">
        <h1>Editar alumno</h1>
        <p>Actualizá los datos del legajo.</p>
    </header>

        <section class="tarjeta-panel">

            <a
                href="<?= esc(url_app("alumnos.php")) ?>"
                class="volver-acceso"
            >
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver al listado
            </a>

            <div class="mb-3">

                <h2>Datos del alumno</h2>

                <p>
                    Modificá solamente los datos necesarios.
                </p>

            </div>

            <?php if ($mensajeError !== ""): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>

                    <?= htmlspecialchars($mensajeError) ?>
                </div>

            <?php endif; ?>

            <form
                action="<?= esc(url_app("actualizar.php")) ?>"
                method="POST"
                id="formularioAlumno"
                novalidate
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars($alumno["id"]) ?>"
                >

                  <?= csrf_campo() ?>

                <div class="row g-4">

                    <div class="col-md-6">

                        <label
                            for="nombre"
                            class="form-label"
                        >
                            Nombre
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            aria-describedby="error-nombre"
                            name="nombre"
                            minlength="2"
                            maxlength="60"
                            autocomplete="given-name"
                            value="<?= htmlspecialchars($alumno["nombre"]) ?>"
                            required
                        >

                        <div class="invalid-feedback" id="error-nombre">
                            Ingresá un nombre válido.
                        </div>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="apellido"
                            class="form-label"
                        >
                            Apellido
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="apellido"
                            aria-describedby="error-apellido"
                            name="apellido"
                            minlength="2"
                            maxlength="60"
                            autocomplete="family-name"
                            value="<?= htmlspecialchars($alumno["apellido"]) ?>"
                            required
                        >

                        <div class="invalid-feedback" id="error-apellido">
                            Ingresá un apellido válido.
                        </div>

                    </div>

                    <div class="col-12">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            aria-describedby="error-correo"
                            name="email"
                            maxlength="100"
                            autocomplete="email"
                            value="<?= htmlspecialchars($alumno["email"]) ?>"
                            required
                        >

                        <div class="invalid-feedback" id="error-correo">
                            Ingresá un correo electrónico válido.
                        </div>

                    </div>

                    <div class="col-12 acciones-formulario">

                        <a
                            href="<?= esc(url_app("alumnos.php")) ?>"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn boton-principal"
                        >
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            Guardar cambios
                        </button>

                    </div>

                </div>

            </form>

        </section>

    </main>

    <script src="<?= esc(url_app("assets/js/app.js")) ?>"></script>

</body>
</html>
