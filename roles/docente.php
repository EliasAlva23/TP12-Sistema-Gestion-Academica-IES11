<?php

$rolesPermitidos = ["Docente"];
require_once __DIR__ . "/../includes/verificar_rol.php";

$titulo = "Panel docente";
$pagina = "panel";
require __DIR__ . "/../includes/header.php";
?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1>Panel docente</h1>
        <p>Consultá el listado de alumnos y los avisos académicos de tu perfil.</p>
    </header>
    <div class="rejilla-accesos">
        <a class="tarjeta-acceso" href="<?= esc(url_app("alumnos.php")) ?>">
            <i class="bi bi-mortarboard" aria-hidden="true"></i>
            <strong>Consultar alumnos</strong>
            <span>Buscar por nombre, apellido o correo.</span>
        </a>
        <a class="tarjeta-acceso" href="<?= esc(url_app("notificaciones.php")) ?>">
            <i class="bi bi-bell" aria-hidden="true"></i>
            <strong>Ver notificaciones</strong>
            <span>Leer los avisos disponibles para tu cuenta.</span>
        </a>
    </div>
</main>
</body>
</html>
