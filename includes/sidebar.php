<?php

$pagina = $pagina ?? "";

$menu = [

    "inicio" => [
        "dashboard.php",
        "bi-speedometer2",
        "Dashboard"
    ],

    "alumnos" => [
        "alumnos.php",
        "bi-mortarboard",
        "Alumnos"
    ],

    "usuarios" => [
        "usuarios.php",
        "bi-people",
        "Usuarios"
    ],

    "reportes" => [
        "reportes.php",
        "bi-bar-chart",
        "Reportes"
    ],

    "notificaciones" => [
        "notificaciones.php",
        "bi-bell",
        "Notificaciones"
    ],

    "configuracion" => [
        "configuracion.php",
        "bi-gear",
        "Configuración"
    ]

];

?>

<aside
    class="sidebar"
    id="sidebar"
>

    <h5 class="sidebar-titulo">
        IES N.º 11
    </h5>

    <nav class="nav flex-column">

        <?php foreach (
            $menu as
            $clave => [$enlace, $icono, $texto]
        ): ?>

            <a
                class="nav-link
                <?= $pagina === $clave
                    ? "active"
                    : "" ?>"
                href="<?= $enlace ?>"
            >

                <i class="bi <?= $icono ?>"></i>

                <?= e($texto) ?>

            </a>

        <?php endforeach; ?>

    </nav>

</aside>

<div
    class="sidebar-fondo"
    id="sidebarFondo"
></div>