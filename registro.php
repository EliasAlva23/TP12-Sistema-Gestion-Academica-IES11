<?php
session_start();

if (isset($_SESSION["usuario_id"])) {
    header("Location: dashboard.php");
    exit;
}

/* Genera un token CSRF para el registro público */
if (empty($_SESSION["csrf_registro"])) {
    $_SESSION["csrf_registro"] = bin2hex(
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

    <title>Registro | Sistema de Gestión Académica</title>

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
                    Portal educativo
                </span>

                <h2>
                    Una comunidad conectada con la educación
                </h2>

                <p>
                    Registrate para acceder a las herramientas
                    académicas disponibles según tu perfil.
                </p>

                <ul class="beneficios-acceso">

                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Acceso personalizado
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Información académica protegida
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Gestión clara y organizada
                    </li>

                </ul>

            </div>

        </section>

        <!-- Panel del formulario -->
        <section class="panel-acceso">

            <div class="formulario-acceso">

                <a href="login.php" class="volver-acceso">
                    <i class="bi bi-arrow-left"></i>
                    Volver a iniciar sesión
                </a>

                <div class="encabezado-formulario">

                    <span>NUEVO USUARIO</span>

                    <h2>Crear una cuenta</h2>

                    <p>
                        Completá tus datos para registrarte
                        en el sistema.
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
                    action="procesar_registro.php"
                    method="POST"
                    id="formularioRegistro"
                    novalidate
                >

                <input
                    type="hidden"
                    name="csrf_registro"
                    value="<?= htmlspecialchars(
                    $_SESSION["csrf_registro"],
                    ENT_QUOTES,
                    "UTF-8"
                    ) ?>"
               >

                    <div class="mb-4">

                        <label for="usuario" class="form-label">
                            Nombre de usuario
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control"
                                id="usuario"
                                name="usuario"
                                minlength="4"
                                maxlength="50"
                                autocomplete="username"
                                placeholder="Ejemplo: marta.salva"
                                required
                            >

                        </div>

                        <div class="form-text">
                            Entre 4 y 50 caracteres.
                        </div>

                    </div>

                    <div class="mb-4">

                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>

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

                        </div>

                    </div>

                    <div class="mb-4">

                        <label for="rol" class="form-label">
                            Tipo de perfil
                        </label>

                        <select
                            class="form-select"
                            id="rol"
                            name="rol"
                            required
                        >
                            <option value="">
                                Seleccione una opción
                            </option>

                            <option value="Alumno">
                                Alumno
                            </option>

                            <option value="Invitado">
                                Invitado
                            </option>
                        </select>

                        <div class="form-text">
                            Los perfiles de docente y administrador
                            requieren autorización institucional.
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-4">

                            <label for="password" class="form-label">
                                Crear contraseña
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
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="Mínimo 8 caracteres"
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

                        <div class="col-md-6 mb-4">

                            <label
                                for="confirmar_password"
                                class="form-label"
                            >
                                Confirmar contraseña
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-shield-check"></i>
                                </span>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirmar_password"
                                    name="confirmar_password"
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="Repetí tu contraseña"
                                    required
                                >

                            </div>

                        </div>

                    </div>

                    <div class="form-check mb-4">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="terminos"
                            name="terminos"
                            value="1"
                            required
                        >

                        <label
                            class="form-check-label"
                            for="terminos"
                        >
                            Acepto las condiciones de uso y el
                            tratamiento de mis datos.
                        </label>

                    </div>

                    <button
                        type="submit"
                        class="btn boton-principal w-100"
                    >
                        <i class="bi bi-person-plus"></i>
                        Crear cuenta
                    </button>

                    <p class="enlace-alternativo">
                        ¿Ya tenés una cuenta?

                        <a href="login.php">
                            Iniciar sesión
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
