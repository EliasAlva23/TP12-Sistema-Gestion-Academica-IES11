<?php

$raizProyecto = str_replace("\\", "/", realpath(__DIR__ . "/.."));
$documentRoot = $_SERVER["DOCUMENT_ROOT"] ?? "";
$raizWeb = $documentRoot !== "" ? realpath($documentRoot) : false;
$base = "";

if ($raizWeb !== false) {
    $prefijo = rtrim(str_replace("\\", "/", $raizWeb), "/") . "/";
    $coincide = DIRECTORY_SEPARATOR === "\\"
        ? strncasecmp($raizProyecto, $prefijo, strlen($prefijo)) === 0
        : str_starts_with($raizProyecto, $prefijo);

    if ($coincide) {
        $base = "/" . implode("/", array_map("rawurlencode", explode("/", substr($raizProyecto, strlen($prefijo)))));
    }
}

define("BASE_URL", $base);

const PANELES_POR_ROL = [
    "Administrador" => "roles/administrador.php",
    "Docente" => "roles/docente.php",
    "Alumno" => "roles/alumno.php",
    "Invitado" => "roles/invitado.php",
];

const PERMISOS_SECCION = [
    "alumnos" => ["Administrador", "Docente"],
    "crud_alumnos" => ["Administrador"],
    "usuarios" => ["Administrador"],
    "reportes" => ["Administrador"],
    "notificaciones" => ["Administrador", "Docente", "Alumno"],
    "crear_notificaciones" => ["Administrador"],
    "configuracion" => ["Administrador"],
];

function url_app(string $ruta, array $parametros = []): string
{
    return BASE_URL . "/" . ltrim($ruta, "/")
        . ($parametros ? "?" . http_build_query($parametros) : "");
}

function redirigir(string $ruta, array $parametros = [], int $estado = 302): never
{
    header("Location: " . url_app($ruta, $parametros), true, $estado);
    exit;
}

function esc(mixed $valor): string
{
    return is_scalar($valor)
        ? htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")
        : "";
}

function puede_acceder(string $seccion, string $rol): bool
{
    return in_array($rol, PERMISOS_SECCION[$seccion] ?? [], true);
}

function iniciar_sesion(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    $sesionIniciada = @session_start([
        "use_strict_mode" => 1,
        "use_only_cookies" => 1,
        "cookie_path" => BASE_URL . "/",
        "cookie_secure" => !empty(getenv("RAILWAY_ENVIRONMENT_ID"))
            || (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off"),
        "cookie_httponly" => true,
        "cookie_samesite" => "Lax",
    ]);

    if (!$sesionIniciada) {
        error_log("No fue posible iniciar la sesión de PHP. Revisá session.save_path.");
        error_acceso(503, "No fue posible iniciar la sesión. Intentá nuevamente.");
    }
}

function cerrar_sesion(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $cookie["path"],
        "domain" => $cookie["domain"],
        "secure" => $cookie["secure"],
        "httponly" => $cookie["httponly"],
        "samesite" => $cookie["samesite"],
    ]);
    session_destroy();
}

function csrf_token(): string
{
    return $_SESSION["csrf"] ??= bin2hex(random_bytes(32));
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . esc(csrf_token()) . '">';
}

function csrf_validar(mixed $token, string $clave = "csrf"): bool
{
    return is_string($token) && isset($_SESSION[$clave]) && hash_equals($_SESSION[$clave], $token);
}

function error_acceso(int $estado, string $mensaje): never
{
    http_response_code($estado);

    if (!empty($GLOBALS["respuestaJson"])) {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["ok" => false, "mensaje" => $mensaje], JSON_UNESCAPED_UNICODE);
    } else {
        header("Content-Type: text/html; charset=utf-8");
        echo '<!doctype html><html lang="es"><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . esc($mensaje) . '</title><main><h1>' . esc($mensaje) . '</h1>'
            . '<p><a href="' . esc(url_app("dashboard.php")) . '">Volver a Inicio</a></p></main></html>';
    }

    exit;
}

if (PHP_SAPI !== "cli") {
    set_exception_handler(static function (Throwable $error): void {
        error_log(get_class($error) . ": " . $error->getMessage());
        error_acceso(500, "Ocurrió un problema al procesar la solicitud.");
    });
}
