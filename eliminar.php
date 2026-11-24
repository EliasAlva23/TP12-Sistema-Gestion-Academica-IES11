<?php

require_once __DIR__ . "/includes/verificar_sesion.php";

$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/includes/verificar_rol.php";
require_once "conexion.php";

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
        header("Location: index.php?mensaje=no_encontrado");
        exit;
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
            header(
                "Location: index.php?mensaje=no_encontrado"
            );
            exit;
        }

        header("Location: index.php?mensaje=eliminado");
        exit;

    } catch (PDOException $error) {

        header(
            "Location: index.php?mensaje=error_eliminar"
        );
        exit;
    }
}

/* Muestra los datos antes de confirmar */
$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: index.php?mensaje=no_encontrado");
    exit;
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
    header("Location: index.php?mensaje=no_encontrado");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Eliminar alumno</title>

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
                Eliminar alumno
            </h1>

            <p class="mb-0">
                Confirmá la eliminación del registro.
            </p>

        </div>
    </header>

    <main class="container py-5">

        <section class="tarjeta-formulario">

            <div class="alert alert-danger" role="alert">

                <h2 class="h5">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    ¿Deseás eliminar este alumno?
                </h2>

                <p class="mb-0">
                    Esta acción eliminará el registro
                    permanentemente.
                </p>

            </div>

            <div class="p-4 my-4 bg-light rounded-3">

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

            <form method="POST" action="eliminar.php">

                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars($alumno["id"]) ?>"
                >
                
                 <?= csrf_campo() ?>

                <div class="acciones-formulario">

                    <a
                        href="index.php"
                        class="btn boton-secundario"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        <i class="bi bi-trash3-fill"></i>
                        Sí, eliminar alumno
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
