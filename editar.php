<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/includes/verificar_rol.php";
require_once "conexion.php";

/* Valida el identificador recibido */
$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: index.php?mensaje=no_encontrado");
    exit;
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
    header("Location: index.php?mensaje=no_encontrado");
    exit;
}

/* Mensajes enviados por actualizar.php */
$error = $_GET["error"] ?? "";

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

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar alumno</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/estilos.css"
    >
</head>

<body>

    <?php require __DIR__ . "/includes/navbar.php"; ?>


    <header class="encabezado">
        <div class="container py-4">

            <p class="etiqueta mb-2">
                Gestión académica
            </p>

            <h1 class="mb-1">
                Editar alumno
            </h1>

            <p class="mb-0">
                Actualizá la información del registro seleccionado.
            </p>

        </div>
    </header>

    <main class="container py-5">

        <section class="tarjeta-formulario">

            <a
                href="index.php"
                class="enlace-volver"
            >
                <i class="bi bi-arrow-left"></i>
                Volver al listado
            </a>

            <div class="titulo-formulario">

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
                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <?= htmlspecialchars($mensajeError) ?>
                </div>

            <?php endif; ?>

            <form
                action="actualizar.php"
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
                            name="nombre"
                            minlength="2"
                            maxlength="60"
                            autocomplete="given-name"
                            value="<?= htmlspecialchars($alumno["nombre"]) ?>"
                            required
                        >

                        <div class="invalid-feedback">
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
                            name="apellido"
                            minlength="2"
                            maxlength="60"
                            autocomplete="family-name"
                            value="<?= htmlspecialchars($alumno["apellido"]) ?>"
                            required
                        >

                        <div class="invalid-feedback">
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
                            name="email"
                            maxlength="100"
                            autocomplete="email"
                            value="<?= htmlspecialchars($alumno["email"]) ?>"
                            required
                        >

                        <div class="invalid-feedback">
                            Ingresá un correo electrónico válido.
                        </div>

                    </div>

                    <div class="col-12 acciones-formulario">

                        <a
                            href="index.php"
                            class="btn boton-secundario"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn boton-principal"
                        >
                            <i class="bi bi-check-circle-fill"></i>
                            Guardar cambios
                        </button>

                    </div>

                </div>

            </form>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script src="assets/js/app.js"></script>

</body>
</html>
