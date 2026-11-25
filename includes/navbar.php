<?php
/*
 * navbar.php
 * Barra de navegación para páginas privadas.
 * Requiere verificar_sesion.php.
 */

$rolActual = $_SESSION["rol"] ?? "";

$puedeVerAlumnos = in_array(
    $rolActual,
    ["Administrador", "Docente"],
    true
);
?>

<nav class="barra-panel" aria-label="Navegación principal">
    <div class="container barra-panel-interior">

        <!-- Marca institucional -->
        <a class="barra-marca"
           href="<?= esc(BASE_URL) ?>/dashboard.php">

            <img
                src="<?= esc(BASE_URL) ?>/assets/img/logo-ies11.png"
                alt="Logo IES N.º 11"
            >

            <span>IES N.º 11</span>
        </a>

        <!-- Navegación -->
        <div class="barra-enlaces">

            <a href="<?= esc(BASE_URL) ?>/dashboard.php">
                <i class="bi bi-house-door"></i>
                Inicio
            </a>

            <a href="<?= esc(url_panel_rol($rolActual)) ?>">
                <i class="bi bi-person-workspace"></i>
                Mi panel
            </a>

            <?php if ($puedeVerAlumnos): ?>
                <a href="<?= esc(BASE_URL) ?>/alumnos.php">
                    <i class="bi bi-mortarboard"></i>
                    Alumnos
                </a>
            <?php endif; ?>

            <a href="<?= esc(BASE_URL) ?>/cambiar_contrasena.php">
                <i class="bi bi-key"></i>
                Cambiar contraseña
            </a>

        </div>

        <!-- Usuario -->
        <div class="barra-usuario">

            <span class="insignia-rol insignia-<?= esc(strtolower($rolActual)) ?>">
                <?= esc($rolActual) ?>
            </span>

            <span class="barra-nombre">
                <i class="bi bi-person-circle"></i>
                <?= esc($_SESSION["usuario"] ?? "") ?>
            </span>

            <a class="barra-salir"
               href="<?= esc(BASE_URL) ?>/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                Salir
            </a>

        </div>

    </div>
</nav>