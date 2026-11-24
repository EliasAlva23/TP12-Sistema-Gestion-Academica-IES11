
<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
/* Permite el acceso a todos los usuarios autenticados */
$rolesPermitidos = [
    "Administrador",
    "Docente",
    "Alumno",
    "Invitado"
];

require_once __DIR__ . "/includes/verificar_rol.php";

$error = "";
$exito = ($_GET["actualizada"] ?? "") === "1";

/* Procesa el formulario */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* Verifica la protección CSRF */
    $token = $_POST["csrf"] ?? null;

    if (!is_string($token) || !csrf_validar($token)) {
        http_response_code(403);
        exit("Solicitud no válida. Volvé a intentarlo.");
    }

    $actual = $_POST["contrasena_actual"] ?? null;
    $nueva = $_POST["contrasena_nueva"] ?? null;
    $confirmacion = $_POST["confirmar_contrasena"] ?? null;

    if (
        !is_string($actual) ||
        !is_string($nueva) ||
        !is_string($confirmacion) ||
        $actual === "" ||
        $nueva === "" ||
        $confirmacion === ""
    ) {
        $error = "Completá todos los campos.";

    } elseif ($nueva !== $confirmacion) {

        $error = "Las contraseñas nuevas no coinciden.";

    } elseif (
        strlen($nueva) < 8 ||
        strlen($nueva) > 72 ||
        !preg_match("/[a-zA-Z]/", $nueva) ||
        !preg_match("/[0-9]/", $nueva)
    ) {

        $error = "La contraseña debe tener entre 8 y 72 caracteres, "
               . "incluyendo letras y números.";

    } else {

        try {

            /* Consulta la contraseña actual */
            $consulta = $conexion->prepare(
                "SELECT password
                 FROM usuarios
                 WHERE id = :id
                 LIMIT 1"
            );

            $consulta->execute([
                ":id" => $_SESSION["usuario_id"]
            ]);

            $hashActual = $consulta->fetchColumn();

            if (
                !is_string($hashActual) ||
                !password_verify($actual, $hashActual)
            ) {

                $error = "La contraseña actual es incorrecta.";

            } elseif (password_verify($nueva, $hashActual)) {

                $error = "La contraseña nueva debe ser diferente de la actual.";

            } else {

                /* Protege la contraseña nueva */
                $nuevoHash = password_hash(
                    $nueva,
                    PASSWORD_DEFAULT
                );

                /* Actualiza únicamente la cuenta autenticada */
                $actualizar = $conexion->prepare(
                    "UPDATE usuarios
                     SET password = :password
                     WHERE id = :id
                       AND password = :hash_actual"
                );

                $actualizar->execute([
                    ":password" => $nuevoHash,
                    ":id" => $_SESSION["usuario_id"],
                    ":hash_actual" => $hashActual
                ]);

                if ($actualizar->rowCount() !== 1) {

                    $error = "No se pudo actualizar la contraseña. "
                           . "Intentá nuevamente.";

                } else {

                    /* Renueva la sesión después del cambio */
                    session_regenerate_id(true);

                    header(
                        "Location: " . BASE_URL .
                        "/cambiar_contrasena.php?actualizada=1"
                    );
                    exit;
                }
            }

        } catch (PDOException $errorBD) {

            error_log(
                "No se pudo actualizar la contraseña del usuario."
            );

            $error = "Ocurrió un problema al guardar la contraseña.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cambiar contraseña | IES N.º 11</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/estilos.css">

  
<style>
    /* Campos del formulario de cambio de contraseña */
    .card-body input[type="password"] {
        display: block;
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        background-color: #ffffff !important;
        color: #24344d !important;
        border: 1px solid #b8c7da !important;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .card-body input[type="password"]:focus {
        border-color: #4774bf !important;
        outline: 2px solid rgba(71, 116, 191, 0.15);
    }
</style>
  
</head>

<body>

<?php require __DIR__ . "/includes/navbar.php"; ?>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-5">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h1 class="h4 mb-3">
                        Cambiar contraseña
                    </h1>

                    <p class="text-muted">
                        Usuario:
                        <strong>
                            <?= esc($_SESSION["usuario"]) ?>
                        </strong>
                    </p>

                    <?php if ($exito): ?>
                        <div class="alert alert-success">
                            Tu contraseña se actualizó correctamente.
                        </div>
                    <?php endif; ?>

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger">
                            <?= esc($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="<?= esc(BASE_URL) ?>/cambiar_contrasena.php">

                        <?= csrf_campo() ?>

                        <div class="mb-3">
                            <label for="actual" class="form-label">
                                Contraseña actual
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="actual"
                                name="contrasena_actual"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="nueva" class="form-label">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="nueva"
                                name="contrasena_nueva"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >

                            <div class="form-text">
                                Entre 8 y 72 caracteres, con letras y números.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirmacion" class="form-label">
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="confirmacion"
                                name="confirmar_contrasena"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Guardar nueva contraseña
                        </button>

                    </form>

                    <a
                        href="<?= esc(BASE_URL) ?>/dashboard.php"
                        class="btn btn-outline-secondary w-100 mt-3"
                    >
                        Volver al panel
                    </a>

                </div>
            </div>

        </div>
    </div>

</main>

</body>
</html>
