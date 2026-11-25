<?php

require_once __DIR__ . "/verificar_sesion.php";

$rolAutorizado = isset($seccionRequerida)
    ? puede_acceder($seccionRequerida, $_SESSION["rol"])
    : in_array($_SESSION["rol"], $rolesPermitidos ?? [], true);

if (!$rolAutorizado) {
    error_acceso(403, "No tenés permiso para acceder a esta sección.");
}
