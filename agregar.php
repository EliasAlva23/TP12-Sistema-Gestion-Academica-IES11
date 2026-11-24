<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/includes/verificar_rol.php";


require_once __DIR__ . "/config/conexion.php";


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
        "Ya existe un alumno registrado con ese correo electrónico.",

    
"servidor" =>
    "No fue posible guardar el alumno. Intentá nuevamente.",


"cuenta" =>
    "La cuenta seleccionada no es válida.",

"cuenta_vinculada" =>
    "Esta cuenta ya está vinculada a otro alumno."

];

$mensajeError =
    $mensajesError[$error] ?? "";


/* Cuentas de alumnos disponibles para vincular */
$consultaCuentas = $conexion->query(
    "SELECT u.id, u.usuario, u.email
     FROM usuarios AS u
     LEFT JOIN alumnos AS a
         ON a.usuario_id = u.id
     WHERE u.rol = 'Alumno'
       AND a.id IS NULL
     ORDER BY u.usuario ASC"
);

$cuentasDisponibles = $consultaCuentas->fetchAll(
    PDO::FETCH_ASSOC
);


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Nuevo alumno</title>

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
                Registro académico
            </p>

            <h1 class="mb-1">
                Nuevo alumno
            </h1>

            <p class="mb-0">
                Completá los datos para agregar un alumno.
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
                    Todos los campos son obligatorios.
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
                action="guardar.php"
                method="POST"
                id="formularioAlumno"
                novalidate
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
                            placeholder="Ejemplo: Marta"
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
                            placeholder="Ejemplo: Salva"
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
                            placeholder="nombre@correo.com"
                            required
                        >

                        <div class="invalid-feedback">
                            Ingresá un correo electrónico válido.
                        </div>

                    </div>

<div class="col-12">

    <label for="usuario_id" class="form-label">
        Vincular cuenta de alumno (opcional)
    </label>

    <select
        class="form-select"
        id="usuario_id"
        name="usuario_id"
    >
        <option value="">
            Sin vincular por ahora
        </option>

        <?php foreach ($cuentasDisponibles as $cuenta): ?>
            <option value="<?= (int) $cuenta["id"] ?>">
                <?= htmlspecialchars($cuenta["usuario"], ENT_QUOTES, "UTF-8") ?>
                -
                <?= htmlspecialchars($cuenta["email"], ENT_QUOTES, "UTF-8") ?>
            </option>
        <?php endforeach; ?>

    </select>

    <div class="form-text">
        Seleccioná una cuenta únicamente si su correo
        coincide con el del alumno.
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
                            Guardar alumno
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