<?php
/*
 * verificar_rol.php
 * --------------------------------------------------------------
 * Restringe una página a ciertos roles.
 *
 * Uso (al comienzo de la página, ANTES de incluir este archivo):
 *
 *     $rolesPermitidos = ["Administrador", "Docente"];
 *     require_once __DIR__ . "/includes/verificar_rol.php";
 *
 * Si el usuario no tiene sesión, lo envía al login.
 * Si tiene sesión pero su rol no está permitido, lo devuelve
 * al dashboard con un aviso de acceso denegado.
 */

require_once __DIR__ . "/verificar_sesion.php";

$rolesPermitidos = $rolesPermitidos ?? [];

if (!in_array($_SESSION["rol"], $rolesPermitidos, true)) {
    header(
        "Location: " . BASE_URL . "/dashboard.php?tipo=error&mensaje="
        . urlencode("No tenés permiso para acceder a esa sección.")
    );
    exit;
}
