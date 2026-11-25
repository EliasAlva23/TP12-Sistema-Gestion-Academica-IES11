<?php

require_once __DIR__ . "/includes/app.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Allow: POST");
    error_acceso(405, "Para cerrar sesión usá el botón Salir.");
}

iniciar_sesion();
if (!csrf_validar($_POST["csrf"] ?? null)) {
    error_acceso(403, "La solicitud para cerrar sesión no es válida.");
}

cerrar_sesion();
redirigir("login.php", [], 303);
