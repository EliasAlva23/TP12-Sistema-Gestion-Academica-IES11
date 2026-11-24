<?php
session_start();

if (isset($_SESSION["usuario_id"])) {
    header("Location: dashboard.php");
    exit;
}

/* Genera un token CSRF para el inicio de sesión */
if (empty($_SESSION["csrf_login"])) {
    $_SESSION["csrf_login"] = bin2hex(
        random_bytes(32)
    );
}

$mensaje = $_GET["mensaje"] ?? "";
$tipo = $_GET["tipo"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión | Sistema de Gestión Académica</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
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
                        alt="Logo del Instituto de Educación Superior N.º 11"
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
                    Acceso institucional
                </span>

                <h2>
                    Bienvenido a tu espacio académico
                </h2>

                <p>
                    Ingresá con tus credenciales para acceder
                    a las funciones disponibles según tu perfil.
                </p>

                <ul class="beneficios-acceso">

                    <li>
                        <i class="bi bi-shield-check"></i>
                        Acceso seguro mediante sesiones
                    </li>

                    <li>
                        <i class="bi bi-person-badge"></i>
                        Funciones organizadas según cada rol
                    </li>

                    <li>
                        <i class="bi bi-lock"></i>
                        Información académica protegida
                    </li>

                </ul>

            </div>

        </section>

        <!-- Formulario -->
        <section class="panel-acceso">

            <div class="formulario-acceso">

                

                <div class="encabezado-formulario">

                    <span>ACCESO AL SISTEMA</span>

                    <h2>Iniciar sesión</h2>

                    <p>
                        Ingresá tu usuario o correo electrónico
                        y tu contraseña.
                    </p>

                </div>

                <?php if ($mensaje !== ""): ?>

                    <div
                        class="alert <?= $tipo === "error"
                            ? "alert-danger"
                            : "alert-success" ?>"
                        role="alert"
                    >
                        <?= htmlspecialchars($mensaje) ?>
                    </div>

                <?php endif; ?>

                <form
                    action="procesar_login.php"
                    method="POST"
                    id="formularioLogin"
                >

<!-- Token de seguridad CSRF -->
<input
    type="hidden"
    name="csrf_login"
    value="<?= htmlspecialchars(
        $_SESSION["csrf_login"],
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
>

                    <div class="mb-4">

                        <label
                            for="identificador"
                            class="form-label"
                        >
                            Usuario o correo electrónico
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control"
                                id="identificador"
                                name="identificador"
                                maxlength="100"
                                autocomplete="username"
                                placeholder="Usuario o correo"
                                required
                                autofocus
                            >

                        </div>

                    </div>

                    <div class="mb-4">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Contraseña
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                maxlength="72"
                                autocomplete="current-password"
                                placeholder="Ingresá tu contraseña"
                                required
                            >

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                id="mostrarPassword"
                                aria-label="Mostrar contraseña"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        class="btn boton-principal w-100"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        Ingresar
                    </button>

                    <p class="enlace-alternativo">
                        ¿Todavía no tenés una cuenta?

                        <a href="registro.php">
                            Registrarte
                        </a>
                    </p>

                </form>

            </div>

        </section>

    </main>

    <script>
        const botonMostrar =
            document.getElementById("mostrarPassword");

        const campoPassword =
            document.getElementById("password");

        botonMostrar.addEventListener("click", function () {

            const esPassword =
                campoPassword.type === "password";

            campoPassword.type =
                esPassword ? "text" : "password";

            this.innerHTML = esPassword
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';

            this.setAttribute(
                "aria-label",
                esPassword
                    ? "Ocultar contraseña"
                    : "Mostrar contraseña"
            );
        });
    </script>

</body>
</html>
