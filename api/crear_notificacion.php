<?php

require_once __DIR__ . "/../includes/app.php";

$respuestaJson = true;
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Allow: POST");
    error_acceso(405, "La creación de avisos requiere POST.");
}

$seccionRequerida = "crear_notificaciones";
require_once __DIR__ . "/../includes/verificar_rol.php";

$esJson = str_starts_with(strtolower($_SERVER["CONTENT_TYPE"] ?? ""), "application/json");
$datos = $esJson ? json_decode(file_get_contents("php://input"), true) : $_POST;
if (!is_array($datos)) {
    error_acceso(400, "La solicitud debe contener datos válidos.");
}

if (!csrf_validar($_SERVER["HTTP_X_CSRF_TOKEN"] ?? $datos["csrf"] ?? null)) {
    error_acceso(403, "La solicitud para crear el aviso no es válida.");
}

$campos = ["tipo" => 50, "titulo" => 150, "mensaje" => 255, "rol_destino" => 30];
foreach ($campos as $campo => $maximo) {
    $valor = $datos[$campo] ?? ($campo === "tipo" ? "sistema" : "");
    if (!is_string($valor) || !mb_check_encoding($valor, "UTF-8") || mb_strlen(trim($valor), "UTF-8") > $maximo) {
        error_acceso(400, "Uno de los campos del aviso no es válido.");
    }
    $datos[$campo] = trim($valor);
}

if ($datos["tipo"] === "" || $datos["titulo"] === "" || $datos["mensaje"] === "") {
    error_acceso(400, "Tipo, título y mensaje son obligatorios.");
}

$rolDestino = $datos["rol_destino"] === "" ? null : $datos["rol_destino"];
$usuarioDestino = $datos["usuario_id"] ?? null;
if ($usuarioDestino !== null && $usuarioDestino !== "") {
    $usuarioDestino = is_int($usuarioDestino) || is_string($usuarioDestino)
        ? filter_var($usuarioDestino, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])
        : false;
} else {
    $usuarioDestino = null;
}

if ($usuarioDestino === false || ($rolDestino !== null && (
    $usuarioDestino !== null || !puede_acceder("notificaciones", $rolDestino)
))) {
    error_acceso(400, "Elegí un único destinatario válido.");
}

try {
    if ($usuarioDestino !== null) {
        $consulta = $conexion->prepare("SELECT rol FROM usuarios WHERE id = :id");
        $consulta->execute([":id" => $usuarioDestino]);
        $rolUsuario = $consulta->fetchColumn();
        if (!$rolUsuario || !puede_acceder("notificaciones", $rolUsuario)) {
            error_acceso(400, "El destinatario no puede recibir avisos.");
        }
    }

    $consulta = $conexion->prepare(
        "INSERT INTO notificaciones (tipo, titulo, mensaje, usuario_id, rol_destino)
         VALUES (:tipo, :titulo, :mensaje, :usuario_id, :rol)"
    );
    $consulta->execute([
        ":tipo" => $datos["tipo"],
        ":titulo" => $datos["titulo"],
        ":mensaje" => $datos["mensaje"],
        ":usuario_id" => $usuarioDestino,
        ":rol" => $rolDestino,
    ]);
} catch (PDOException $error) {
    error_log("Creación de aviso: " . $error->getMessage());
    error_acceso(503, "No fue posible crear el aviso.");
}

http_response_code(201);
echo json_encode(["ok" => true, "id" => (int) $conexion->lastInsertId()]);
