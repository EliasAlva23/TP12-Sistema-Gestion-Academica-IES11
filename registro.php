<?php
require_once __DIR__ . "/includes/app.php";

iniciar_sesion();

if (isset($_SESSION["usuario_id"])) {
    redirigir("dashboard.php");
}

/* Genera un token CSRF para el registro público */
if (empty($_SESSION["csrf_registro"])) {
    $_SESSION["csrf_registro"] = bin2hex(
        random_bytes(32)
    );
}

$mensaje = is_string($_GET["mensaje"] ?? null) ? $_GET["mensaje"] : "";
$tipo = $_GET["tipo"] ?? "";
$titulo = "Crear una cuenta";
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
                    Portal educativo
                </span>

                <p class="titulo-institucional">
                    Una comunidad conectada con la educación
                </p>

                <p>
                    Registrate para acceder a las herramientas
                    académicas disponibles según tu perfil.
                </p>

                <ul class="beneficios-acceso">

                    <li>
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        Acceso personalizado
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        Información académica protegida
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        Gestión clara y organizada
                    </li>

                </ul>

            </div>

        </section>

        <!-- Panel del formulario -->
        <div class="panel-acceso">

            <section class="formulario-acceso tarjeta-panel" aria-labelledby="titulo-acceso">

                <a href="<?= esc(url_app("login.php")) ?>" class="volver-acceso">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Volver a iniciar sesión
                </a>

                <header class="encabezado-formulario">

                    <span>NUEVO USUARIO</span>

                    <h1 id="titulo-acceso">Crear una cuenta</h1>

                    <p>
                        Completá tus datos para registrarte
                        en el sistema.
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
                    action="<?= esc(url_app("procesar_registro.php")) ?>"
                    method="POST"
                    id="formularioRegistro"
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
                                <i class="bi bi-person" aria-hidden="true"></i>
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
                                <i class="bi bi-envelope" aria-hidden="true"></i>
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
                                    <i class="bi bi-lock" aria-hidden="true"></i>
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
                                    aria-controls="password"
                                    aria-label="Mostrar contraseña"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
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
                                    <i class="bi bi-shield-check" aria-hidden="true"></i>
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
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                        Crear cuenta
                    </button>

                    <p class="enlace-alternativo">
                        ¿Ya tenés una cuenta?

                        <a href="<?= esc(url_app("login.php")) ?>">
                            Iniciar sesión
                        </a>
                    </p>

                </form>

            </section>

        </div>

    </main>

    <script src="<?= esc(url_app("assets/js/app.js")) ?>" defer></script>

</body>
</html>
