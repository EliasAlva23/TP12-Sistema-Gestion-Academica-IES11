<?php
require_once __DIR__ . "/includes/app.php";

iniciar_sesion();

/* Si ya inició sesión, puede cambiar su contraseña
   desde el panel del sistema. */
if (isset($_SESSION["usuario_id"])) {
    redirigir("cambiar_contrasena.php");
}

$titulo = "Recuperar contraseña";
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
                        alt=""
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
                    Seguridad de la cuenta
                </span>

                <p class="titulo-institucional">
                    Recuperá el acceso a tu cuenta
                </p>

                <p>Solicitá ayuda a la administración del instituto para volver a entrar al sistema.</p>

            </div>

        </section>

        <!-- Información de recuperación -->
        <div class="panel-acceso">

            <section class="formulario-acceso tarjeta-panel" aria-labelledby="titulo-acceso">

                <header class="encabezado-formulario">

                    <h1 id="titulo-acceso">¿Olvidaste tu contraseña?</h1>


                </header>

                <p class="alert alert-info">La recuperación automática todavía no está disponible.</p>
                <p class="enlace-alternativo"><a href="<?= esc(url_app("login.php")) ?>">Volver al inicio de sesión</a></p>

            </section>

        </div>

    </main>

</body>
</html>
