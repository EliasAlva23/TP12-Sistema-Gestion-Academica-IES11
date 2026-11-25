<?php

require_once __DIR__ . "/app.php";

iniciar_sesion();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

function sesion_requerida(string $mensaje): never
{
    if (!empty($GLOBALS["respuestaJson"])) {
        error_acceso(401, $mensaje);
    }

    redirigir("login.php", ["tipo" => "error", "mensaje" => $mensaje]);
}

if (empty($_SESSION["usuario_id"])) {
    sesion_requerida("Iniciá sesión para acceder a esta página.");
}

if (time() - ($_SESSION["ultima_actividad"] ?? time()) > 1800) {
    cerrar_sesion();
    sesion_requerida("Tu sesión expiró por inactividad. Ingresá de nuevo.");
}

require_once __DIR__ . "/../config/conexion.php";

try {
    $consultaSesion = $conexion->prepare("SELECT usuario, email, rol FROM usuarios WHERE id = :id");
    $consultaSesion->execute([":id" => $_SESSION["usuario_id"]]);
    $datosSesion = $consultaSesion->fetch();
} catch (PDOException $error) {
    error_log("Verificación de sesión: " . $error->getMessage());
    error_acceso(503, "No fue posible verificar tu sesión.");
}

if (!$datosSesion || !isset(PANELES_POR_ROL[$datosSesion["rol"]])) {
    cerrar_sesion();
    sesion_requerida("Tu cuenta no está disponible.");
}

$_SESSION["ultima_actividad"] = time();
$_SESSION["usuario"] = $datosSesion["usuario"];
$_SESSION["email"] = $datosSesion["email"];
$_SESSION["rol"] = $datosSesion["rol"];
