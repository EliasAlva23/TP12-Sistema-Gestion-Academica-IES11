<?php

$seccionRequerida = "crud_alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";

/* Procesa la eliminación confirmada */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* Verifica el token de seguridad CSRF */
    $tokenCsrf = $_POST["csrf"] ?? null;

    if (
        !is_string($tokenCsrf) ||
        !csrf_validar($tokenCsrf)
    ) {
        http_response_code(403);

        exit(
            "Solicitud no válida. Volvé al listado " .
            "de alumnos e intentá nuevamente."
        );
    }

    $id = filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );

    if (!$id || $id < 1) {
        redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
    }

    try {

        $consulta = $conexion->prepare(
            "DELETE FROM alumnos
             WHERE id = :id"
        );

        $consulta->execute([
            ":id" => $id
        ]);

        if ($consulta->rowCount() === 0) {
            redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
        }

        redirigir("alumnos.php", ["mensaje" => "eliminado"], 303);

    } catch (PDOException $error) {

        redirigir("alumnos.php", ["mensaje" => "error_eliminar"], 303);
    }
}

/* Muestra los datos antes de confirmar */
$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
}

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

$titulo = "Eliminar alumno";
$pagina = "alumnos";
require __DIR__ . "/includes/header.php";
?>


    <main id="contenido-principal" tabindex="-1" class="pagina-contenido pagina-formulario">
    <header class="encabezado-pagina">
        <h1>Eliminar alumno</h1>
        <p>Revisá el legajo antes de confirmar la baja.</p>
    </header>

        <section class="tarjeta-panel">

            <div class="alert alert-danger" role="alert">

                <h2 class="h5">
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                    ¿Deseás eliminar este alumno?
                </h2>

                <p class="mb-0">
                    Esta acción eliminará el registro
                    permanentemente.
                </p>

            </div>

            <div class="p-3 my-4 bg-body-tertiary rounded-3">

                <p>
                    <strong>Nombre:</strong>

                    <?= htmlspecialchars(
                        $alumno["nombre"] . " " .
                        $alumno["apellido"]
                    ) ?>
                </p>

                <p class="mb-0">
                    <strong>Correo electrónico:</strong>

                    <?= htmlspecialchars($alumno["email"]) ?>
                </p>

            </div>

            <form method="POST" action="<?= esc(url_app("eliminar.php")) ?>">

                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars($alumno["id"]) ?>"
                >
                
                 <?= csrf_campo() ?>

                <div class="acciones-formulario">

                    <a
                        href="<?= esc(url_app("alumnos.php")) ?>"
                        class="btn btn-outline-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                        Sí, eliminar alumno
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
