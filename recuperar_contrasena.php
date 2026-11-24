
<?php
session_start();

/* Si ya inició sesión, puede cambiar su contraseña
   desde el panel del sistema. */
if (isset($_SESSION["usuario_id"])) {
    header("Location: cambiar_contrasena.php");
    exit;
}

/* Token de seguridad para la solicitud */
if (empty($_SESSION["csrf_recuperacion"])) {
    $_SESSION["csrf_recuperacion"] = bin2hex(
        random_bytes(32)
    );
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

    <title>Recuperar contraseña | IES N.º 11</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/estilos.css"
    >
</head>

<body class="pagina-acceso">

    <main class="contenedor-acceso">

        <!-- Panel institucional -->
        <section class="panel-institucional">

            <div class="marca-institucional">

                <div class="icono-institucional">

                    <img
                        src="assets/img/logo-ies11.png"
                        alt="Logo del IES N.º 11"
                        class="logo-ies"
                    >

                </div>

                <div>
                    <h1>IES N.º 11</h1>
                    <p>Sistema de Gestión Académica</p>
                </div>

            </div>

            <div class="contenido-institucional">

                <span class="etiqueta-acceso">
                    Seguridad de la cuenta
                </span>

                <h2>
                    Recuperá el acceso a tu cuenta
                </h2>

                <p>
                    Protegemos tu información académica
                    mediante un proceso seguro de
                    recuperación de contraseña.
                </p>

                <ul class="beneficios-acceso">

                    <li>
                        <i class="bi bi-envelope-check"></i>
                        Verificación mediante correo electrónico
                    </li>

                    <li>
                        <i class="bi bi-shield-lock"></i>
                        Enlace de recuperación temporal
                    </li>

                    <li>
                        <i class="bi bi-key"></i>
                        Creación de una nueva contraseña
                    </li>

                </ul>

            </div>

        </section>

        <!-- Formulario de recuperación -->
        <section class="panel-acceso">

            <div class="formulario-acceso">

                <div class="encabezado-formulario">

                    <span>RECUPERACIÓN DE CUENTA</span>

                    <h2>¿Olvidaste tu contraseña?</h2>

                    <p>
                        Ingresá el correo electrónico
                        asociado a tu cuenta.
                    </p>

                </div>

<?php if (($_GET["enviado"] ?? "") === "1"): ?>

    <div class="alert alert-success" role="status">
        <i class="bi bi-check-circle"></i>

        Si el correo electrónico está registrado,
        se generó una solicitud de recuperación.

        Durante las pruebas, revisá el archivo
        privado de recuperación.
    </div>

<?php endif; ?>

                <form
                    action="solicitar_recuperacion.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="csrf_recuperacion"
                        value="<?= htmlspecialchars(
                            $_SESSION["csrf_recuperacion"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                    >

                    <div class="mb-4">

                        <label
                            for="correo"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>

                            <input
                                type="email"
                                class="form-control"
                                id="correo"
                                name="correo"
                                maxlength="254"
                                autocomplete="email"
                                placeholder="ejemplo@correo.com"
                                required
                            >

                        </div>

                    </div>

                    <button
                        type="submit"
                        class="btn boton-principal w-100"
                    >
                        <i class="bi bi-send"></i>
                        Solicitar recuperación
                    </button>

                    <p class="enlace-alternativo">

                        <a href="login.php">
                            <i class="bi bi-arrow-left"></i>
                            Volver al inicio de sesión
                        </a>

                    </p>

                </form>

            </div>

        </section>

    </main>

</body>
</html>
