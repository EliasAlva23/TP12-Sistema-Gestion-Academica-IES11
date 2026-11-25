<?php
require_once __DIR__ . "/includes/app.php";

iniciar_sesion();

if (isset($_SESSION["usuario_id"])) {
    redirigir("dashboard.php");
}

/* Genera un token CSRF para el inicio de sesión */
if (empty($_SESSION["csrf_login"])) {
    $_SESSION["csrf_login"] = bin2hex(
        random_bytes(32)
    );
}

$mensaje = is_string($_GET["mensaje"] ?? null) ? $_GET["mensaje"] : "";
$tipo = $_GET["tipo"] ?? "";
$titulo = "Iniciar sesión";
$paginaPublica = true;
require __DIR__ . "/includes/header.php";
?>

    <main id="contenido-principal" tabindex="-1" class="contenedor-acceso">

        <!-- Panel institucional -->
        <section class="panel-institucional">

            <div class="marca-institucional">

                <div class="icono-institucional">

                    <img
                        src="<?= esc(url_app("assets/img/logo-ies11.png")) ?>"
                        alt="Logo del Instituto de Educación Superior N.º 11"
                        class="logo-ies"
                    >

                </div>

                <div>
                    <p class="marca-nombre">IES N.º 11</p>
                    <p>Sistema de Gestión Académica</p>
                </div>

            </div>

            <div class="contenido-institucional">

                <span class="etiqueta-acceso">
                    Acceso institucional
                </span>

                <p class="titulo-institucional">
                    Bienvenido a tu espacio académico
                </p>

                <p>
                    Ingresá con tus credenciales para acceder
                    a las funciones disponibles según tu perfil.
                </p>

                <ul class="beneficios-acceso">

                    <li>
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        Acceso seguro mediante sesiones
                    </li>

                    <li>
                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                        Funciones organizadas según cada rol
                    </li>

                    <li>
                        <i class="bi bi-lock" aria-hidden="true"></i>
                        Información académica protegida
                    </li>

                </ul>

            </div>

        </section>

        <!-- Formulario -->
        <div class="panel-acceso">

            <section class="formulario-acceso tarjeta-panel" aria-labelledby="titulo-acceso">

                

                <header class="encabezado-formulario">

                    <h1 id="titulo-acceso">Iniciar sesión</h1>

                    <p>
                        Ingresá tu usuario o correo electrónico
                        y tu contraseña.
                    </p>

                </header>

                <?php if ($mensaje !== ""): ?>

                    <div
                        class="alert <?= $tipo === "error"
                            ? "alert-danger"
                            : "alert-success" ?>"
                        role="alert"
                    >
                        <?= esc($mensaje) ?>
                    </div>

                <?php endif; ?>

                <form
                    action="<?= esc(url_app("procesar_login.php")) ?>"
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
                                <i class="bi bi-person" aria-hidden="true"></i>
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
                                <i class="bi bi-lock" aria-hidden="true"></i>
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
                                aria-controls="password"
                                aria-label="Mostrar contraseña"
                            >
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        class="btn boton-principal w-100"
                    >
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        Ingresar
                    </button>

                    <p class="enlace-alternativo">
                        ¿Todavía no tenés una cuenta?

                        <a href="<?= esc(url_app("registro.php")) ?>">
                            Registrarte
                        </a>
                        <br>
                        <a href="<?= esc(url_app("recuperar_contrasena.php")) ?>">Recuperar contraseña</a>
                    </p>

                </form>

            </section>

        </div>

    </main>

    <script src="<?= esc(url_app("assets/js/app.js")) ?>" defer></script>

</body>
</html>
