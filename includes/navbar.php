
<?php
/*
 * navbar.php
 * Barra superior compartida por todas las páginas privadas.
 * Requiere que verificar_sesion.php ya haya sido incluido.
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

        <!-- Logo del instituto -->
        <a
            class="barra-marca"
            href="<?= esc(BASE_URL) ?>/dashboard.php"
        >
            <img
                src="<?= esc(BASE_URL) ?>/assets/img/logo-ies11.png"
                alt="Logo IES N.º 11"
            >

            <span>IES N.º 11</span>
        </a>

        <!-- Menú de navegación -->
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

                <a href="<?= esc(BASE_URL) ?>/index.php">
                    <i class="bi bi-people"></i>
                    Alumnos
                </a>

            <?php endif; ?>

            <!-- Opción permanente para cambiar contraseña -->
            <a
                href="<?= esc(BASE_URL) ?>/cambiar_contrasena.php"
                title="Cambiar mi contraseña"
            >
                <i class="bi bi-key"></i>
                Cambiar contraseña
            </a>

        </div>

        <!-- Información del usuario -->
        <div class="barra-usuario">

            <span
                class="insignia-rol insignia-<?= esc(strtolower($rolActual)) ?>"
            >
                <?= esc($rolActual) ?>
            </span>

            <span class="barra-nombre">
                <i class="bi bi-person-circle"></i>
                <?= esc($_SESSION["usuario"] ?? "") ?>
            </span>

            <!-- Cerrar sesión -->
            <a
                class="barra-salir"
                href="<?= esc(BASE_URL) ?>/logout.php"
            >
                <i class="bi bi-box-arrow-right"></i>
                Salir
            </a>

        </div>

    </div>

</nav>
