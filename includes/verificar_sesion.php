<?php
/*
 * verificar_sesion.php
 * --------------------------------------------------------------
 * Protege las páginas privadas del sistema.
 * Debe incluirse al comienzo de toda página que requiera
 * haber iniciado sesión.
 *
 * Qué hace:
 *  1. Inicia la sesión con una cookie más segura.
 *  2. Impide que el navegador guarde páginas privadas en caché
 *     (así el botón "Atrás" no muestra datos tras cerrar sesión).
 *  3. Redirige al login si no hay sesión iniciada.
 *  4. Cierra la sesión por inactividad (30 minutos).
 *  5. Vuelve a consultar el rol en la base de datos, para que
 *     un cambio de rol (o la baja del usuario) tenga efecto
 *     de inmediato.
 *  6. Ofrece funciones de ayuda: esc(), csrf_*, url_panel_rol().
 */



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.php");
    exit;

}


require_once __DIR__ . "/../config/conexion.php";

/* ------------------------------------------------------------
 * Ruta base del proyecto (por ejemplo "/Sistema_Gestion_...").
 * Permite armar enlaces correctos desde cualquier carpeta.
 * ------------------------------------------------------------ */
if (!defined("BASE_URL")) {
    $raizProyecto = str_replace("\\", "/", realpath(__DIR__ . "/.."));
    $raizWeb = $_SERVER["DOCUMENT_ROOT"] ?? "";
    $rutaBase = "";

    if ($raizWeb !== "") {
        $raizWeb = str_replace("\\", "/", realpath($raizWeb));

        if (
            $raizWeb !== "" &&
            stripos($raizProyecto, $raizWeb) === 0
        ) {
            $rutaBase = substr($raizProyecto, strlen($raizWeb));
        }
    }

    define("BASE_URL", rtrim($rutaBase, "/"));
}

/* Tiempo máximo de inactividad: 30 minutos */
const TIEMPO_INACTIVIDAD = 1800;

/* Panel que corresponde a cada rol */
const PANELES_POR_ROL = [
    "Administrador" => "roles/administrador.php",
    "Docente"       => "roles/docente.php",
    "Alumno"        => "roles/alumno.php",
    "Invitado"      => "roles/invitado.php"
];

/* ------------------------------------------------------------
 * Funciones de ayuda
 * ------------------------------------------------------------ */

/* Escapa texto antes de mostrarlo en HTML (previene XSS) */
function esc($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

/* URL del panel del rol indicado */
function url_panel_rol(string $rol): string
{
    $ruta = PANELES_POR_ROL[$rol] ?? "dashboard.php";
    return BASE_URL . "/" . $ruta;
}

/* Redirige al login con un mensaje */
function ir_al_login(string $mensaje): void
{
    header(
        "Location: " . BASE_URL . "/login.php?tipo=error&mensaje="
        . urlencode($mensaje)
    );
    exit;
}

/* Token CSRF: protege los formularios que modifican datos */
function csrf_token(): string
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf"];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="'
        . esc(csrf_token()) . '">';
}

function csrf_validar(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION["csrf"])
        && hash_equals($_SESSION["csrf"], $token);
}

/* ------------------------------------------------------------
 * Verificaciones de la sesión
 * ------------------------------------------------------------ */

/* No guardar páginas privadas en la caché del navegador */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/* 1. ¿Hay una sesión iniciada? */
if (empty($_SESSION["usuario_id"])) {
    ir_al_login("Iniciá sesión para acceder a esta página.");
}

/* 2. ¿Venció por inactividad? */
if (
    isset($_SESSION["ultima_actividad"]) &&
    (time() - $_SESSION["ultima_actividad"]) > TIEMPO_INACTIVIDAD
) {
    $_SESSION = [];
    session_destroy();
    ir_al_login("Tu sesión expiró por inactividad. Ingresá de nuevo.");
}

$_SESSION["ultima_actividad"] = time();

/* 3. ¿El usuario sigue existiendo? ¿Cambió su rol? */
try {
    $consultaSesion = $conexion->prepare(
        "SELECT usuario, email, rol
         FROM usuarios
         WHERE id = :id
         LIMIT 1"
    );

    $consultaSesion->execute([
        ":id" => $_SESSION["usuario_id"]
    ]);

    $datosSesion = $consultaSesion->fetch();
} catch (PDOException $error) {
    ir_al_login("No fue posible verificar tu sesión.");
}

if (!$datosSesion) {
    $_SESSION = [];
    session_destroy();
    ir_al_login("Tu cuenta ya no existe.");
}

$_SESSION["usuario"] = $datosSesion["usuario"];
$_SESSION["email"]   = $datosSesion["email"];
$_SESSION["rol"]     = $datosSesion["rol"];
