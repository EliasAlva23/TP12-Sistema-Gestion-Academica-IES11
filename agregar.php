<?php

$seccionRequerida = "crud_alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";




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


$titulo = "Agregar alumno";
$pagina = "alumnos";
require __DIR__ . "/includes/header.php";
?>


    <main id="contenido-principal" tabindex="-1" class="pagina-contenido pagina-formulario">
    <header class="encabezado-pagina">
        <h1>Agregar alumno</h1>
        <p>Registrá los datos del legajo.</p>
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
                    Todos los campos son obligatorios.
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
                action="<?= esc(url_app("guardar.php")) ?>"
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
                            aria-describedby="error-nombre"
                            name="nombre"
                            minlength="2"
                            maxlength="60"
                            autocomplete="given-name"
                            placeholder="Ejemplo: Marta"
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
                            placeholder="Ejemplo: Salva"
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
                            placeholder="nombre@correo.com"
                            required
                        >

                        <div class="invalid-feedback" id="error-correo">
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
                            Guardar alumno
                        </button>

                    </div>

                </div>

            </form>

        </section>

    </main>

    <script src="<?= esc(url_app("assets/js/app.js")) ?>"></script>

</body>
</html>
