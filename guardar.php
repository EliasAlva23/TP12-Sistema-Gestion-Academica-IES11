<?php

$seccionRequerida = "crud_alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";

/* Solo permite solicitudes enviadas desde el formulario */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("alumnos.php");
}

/* Verifica el token de seguridad CSRF */
$tokenCsrf = $_POST["csrf"] ?? null;

if (
    !is_string($tokenCsrf) ||
    !csrf_validar($tokenCsrf)
) {
    http_response_code(403);

    exit(
        "Solicitud no válida. Volvé al formulario " .
        "de alumnos e intentá nuevamente."
    );
}

/* Recibe y limpia los datos */
$nombre = trim(is_string($_POST["nombre"] ?? null) ? $_POST["nombre"] : "");
$apellido = trim(is_string($_POST["apellido"] ?? null) ? $_POST["apellido"] : "");
$email = trim(is_string($_POST["email"] ?? null) ? $_POST["email"] : "");

/* Convierte nombre y apellido a formato correcto */
$nombre = mb_convert_case(
    $nombre,
    MB_CASE_TITLE,
    "UTF-8"
);

$apellido = mb_convert_case(
    $apellido,
    MB_CASE_TITLE,
    "UTF-8"
);

$email = strtolower($email);


/* Recibe la cuenta seleccionada para vincular */
$usuarioId = trim(is_string($_POST["usuario_id"] ?? null) ? $_POST["usuario_id"] : "");


/* Validaciones del servidor */
if (
    $nombre === "" ||
    $apellido === "" ||
    $email === ""
) {
    redirigir("agregar.php", ["error" => "campos"], 303);
}

if (
    mb_strlen($nombre) < 2 ||
    mb_strlen($nombre) > 60 ||
    mb_strlen($apellido) < 2 ||
    mb_strlen($apellido) > 60
) {
    redirigir("agregar.php", ["error" => "longitud"], 303);
}

if (
    !preg_match(
        "/^[\p{L}\s'-]+$/u",
        $nombre
    ) ||
    !preg_match(
        "/^[\p{L}\s'-]+$/u",
        $apellido
    )
) {
    redirigir("agregar.php", ["error" => "nombre"], 303);
}

if (
    strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    redirigir("agregar.php", ["error" => "email"], 303);
}


/* Valida el identificador de la cuenta seleccionada */
if (
    $usuarioId !== "" &&
    (
        !ctype_digit($usuarioId) ||
        (int) $usuarioId <= 0
    )
) {
    redirigir("agregar.php", ["error" => "cuenta"], 303);
}

try {
    if ($usuarioId !== "") {
        $conexion->beginTransaction();

        $consultaCuenta = $conexion->prepare(
            "SELECT email FROM usuarios WHERE id = :id AND rol = 'Alumno' FOR UPDATE"
        );
        $consultaCuenta->execute([":id" => (int) $usuarioId]);
        $correoCuenta = $consultaCuenta->fetchColumn();

        if ($correoCuenta === false || strtolower(trim($correoCuenta)) !== $email) {
            $conexion->rollBack();
            redirigir("agregar.php", ["error" => "cuenta"], 303);
        }

        $consultaVinculo = $conexion->prepare("SELECT id FROM alumnos WHERE usuario_id = :id LIMIT 1");
        $consultaVinculo->execute([":id" => (int) $usuarioId]);
        if ($consultaVinculo->fetchColumn()) {
            $conexion->rollBack();
            redirigir("agregar.php", ["error" => "cuenta_vinculada"], 303);
        }
    }

    $consulta = $conexion->prepare(
        "INSERT INTO alumnos (nombre, apellido, email, usuario_id)
         VALUES (:nombre, :apellido, :email, :usuario_id)"
    );
    $consulta->execute([
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":email" => $email,
        ":usuario_id" => $usuarioId === "" ? null : (int) $usuarioId,
    ]);

    if ($conexion->inTransaction()) {
        $conexion->commit();
    }

    redirigir("alumnos.php", ["mensaje" => "guardado"], 303);

} catch (PDOException $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    /* Identifica el código de error de MySQL */
    $codigoMysql = (int) ($error->errorInfo[1] ?? 0);

    /* Error 1062: valor duplicado */
    if ($codigoMysql === 1062) {

        $detalle = $error->errorInfo[2] ?? "";

        /* Cuenta vinculada a otro alumno */
        if (str_contains($detalle, "uq_alumnos_usuario_id")) {
            redirigir("agregar.php", ["error" => "cuenta_vinculada"], 303);
        }

        /* Correo electrónico duplicado */
        redirigir("agregar.php", ["error" => "duplicado"], 303);
    }

    /* La cuenta seleccionada ya no existe */
    if ($codigoMysql === 1452) {
        redirigir("agregar.php", ["error" => "cuenta"], 303);
    }

    error_log("Alta de alumno: " . $error->getMessage());
    redirigir("agregar.php", ["error" => "servidor"], 303);
}
