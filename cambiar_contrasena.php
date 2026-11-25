<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
/* Permite el acceso a todos los usuarios autenticados */
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

        redirigir("cambiar_contrasena.php", ["actualizada" => 1], 303);
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

$titulo = "Cambiar contraseña";
$pagina = "contrasena";
require __DIR__ . "/includes/header.php";
?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido pagina-formulario-estrecha">
    <header class="encabezado-pagina">
        <h1>Cambiar contraseña</h1>
        <p>Cuenta: <?= esc($_SESSION["usuario"]) ?></p>
    </header>
    <section class="tarjeta-panel" aria-label="Formulario de cambio de contraseña">
        <?php if ($exito): ?>
            <p class="alert alert-success" role="status">Tu contraseña se actualizó correctamente.</p>
        <?php endif; ?>
        <?php if ($error !== ""): ?>
            <p class="alert alert-danger" role="alert"><?= esc($error) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= esc(url_app("cambiar_contrasena.php")) ?>">
            <?= csrf_campo() ?>
            <div class="mb-3">
                <label for="actual" class="form-label">Contraseña actual</label>
                <input type="password" class="form-control" id="actual" name="contrasena_actual"
                       autocomplete="current-password" required>
            </div>
            <div class="mb-3">
                <label for="nueva" class="form-label">Nueva contraseña</label>
                <input type="password" class="form-control" id="nueva" name="contrasena_nueva"
                       autocomplete="new-password" minlength="8" maxlength="72"
                       aria-describedby="requisitos-clave" required>
                <p class="form-text" id="requisitos-clave">Entre 8 y 72 caracteres, con letras y números.</p>
            </div>
            <div class="mb-4">
                <label for="confirmacion" class="form-label">Confirmar nueva contraseña</label>
                <input type="password" class="form-control" id="confirmacion" name="confirmar_contrasena"
                       autocomplete="new-password" minlength="8" maxlength="72" required>
            </div>
            <div class="acciones-formulario">
                <a href="<?= esc(url_app("dashboard.php")) ?>" class="btn btn-outline-secondary">Volver a Inicio</a>
                <button type="submit" class="btn boton-principal">Guardar nueva contraseña</button>
            </div>
        </form>
    </section>
</main>

</body>
</html>
