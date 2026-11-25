<?php

$rolActual = $_SESSION["rol"];
$menu = [
    "inicio" => ["dashboard.php", "bi-house-door", "Inicio"],
    "panel" => [PANELES_POR_ROL[$rolActual], "bi-person-workspace", "Mi panel"],
];

foreach ([
    "alumnos" => ["alumnos.php", "bi-mortarboard", "Alumnos"],
    "usuarios" => ["usuarios.php", "bi-people", "Usuarios"],
    "reportes" => ["reportes.php", "bi-bar-chart", "Reportes"],
    "notificaciones" => ["notificaciones.php", "bi-bell", "Notificaciones"],
    "configuracion" => ["configuracion.php", "bi-gear", "Configuración"],
] as $seccion => $item) {
    if (puede_acceder($seccion, $rolActual)) {
        $menu[$seccion] = $item;
    }
}

$menu["contrasena"] = ["cambiar_contrasena.php", "bi-key", "Cambiar contraseña"];
?>

<nav class="navbar navbar-expand-xl barra-panel" data-bs-theme="dark" aria-label="Navegación principal">
    <div class="container-xxl">
        <a class="navbar-brand barra-marca" href="<?= esc(url_app("dashboard.php")) ?>">
            <img src="<?= esc(url_app("assets/img/logo-ies11.png")) ?>" alt="" width="34" height="34">
            IES N.º 11
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-haspopup="dialog" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="menuPrincipal" aria-labelledby="tituloMenuPrincipal">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title h5" id="tituloMenuPrincipal">Menú principal</h2>
                <button class="btn-close" type="button" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
            </div>
            <div class="offcanvas-body align-items-xl-center">
                <ul class="navbar-nav flex-grow-1">
                    <?php foreach ($menu as $clave => [$ruta, $icono, $etiqueta]): ?>
                        <?php $activo = ($pagina ?? "") === $clave; ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $activo ? " active" : "" ?>"
                               href="<?= esc(url_app($ruta)) ?>"<?= $activo ? ' aria-current="page"' : "" ?>>
                                <i class="bi <?= esc($icono) ?>" aria-hidden="true"></i> <?= esc($etiqueta) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="barra-cuenta">
                    <span class="insignia-rol insignia-<?= esc(strtolower($rolActual)) ?>"><?= esc($rolActual) ?></span>
                    <form method="post" action="<?= esc(url_app("logout.php")) ?>">
                        <?= csrf_campo() ?>
                        <button class="btn btn-sm btn-outline-light" type="submit">
                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
