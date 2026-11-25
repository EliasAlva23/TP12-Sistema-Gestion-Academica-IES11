<?php

$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/../includes/verificar_rol.php";

$titulo = "Panel de administración";
$pagina = "panel";
require __DIR__ . "/../includes/header.php";
?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1>Panel de administración</h1>
        <p>Gestioná los legajos, las cuentas y el entorno del sistema.</p>
    </header>
    <div class="rejilla-accesos">
        <a class="tarjeta-acceso" href="<?= esc(url_app("alumnos.php")) ?>">
            <i class="bi bi-mortarboard" aria-hidden="true"></i>
            <strong>Gestionar alumnos</strong>
            <span>Registrar, buscar y actualizar legajos.</span>
        </a>
        <a class="tarjeta-acceso" href="<?= esc(url_app("usuarios.php")) ?>">
            <i class="bi bi-people" aria-hidden="true"></i>
            <strong>Administrar cuentas y roles</strong>
            <span>Buscar usuarios y gestionar sus permisos.</span>
        </a>
        <a class="tarjeta-acceso" href="<?= esc(url_app("configuracion.php")) ?>">
            <i class="bi bi-gear" aria-hidden="true"></i>
            <strong>Consultar diagnóstico</strong>
            <span>Revisar el estado y los datos del entorno.</span>
        </a>
    </div>
</main>
</body>
</html>
