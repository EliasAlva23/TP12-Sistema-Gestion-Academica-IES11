<?php

require_once __DIR__ . "/includes/verificar_sesion.php";

$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/includes/verificar_rol.php";
require_once "conexion.php";

/* Solo permite solicitudes enviadas desde el formulario */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
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
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$email = trim($_POST["email"] ?? "");

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
$usuarioId = trim((string) ($_POST["usuario_id"] ?? ""));


/* Validaciones del servidor */
if (
    $nombre === "" ||
    $apellido === "" ||
    $email === ""
) {
    header("Location: agregar.php?error=campos");
    exit;
}

if (
    mb_strlen($nombre) < 2 ||
    mb_strlen($nombre) > 60 ||
    mb_strlen($apellido) < 2 ||
    mb_strlen($apellido) > 60
) {
    header("Location: agregar.php?error=longitud");
    exit;
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
    header("Location: agregar.php?error=nombre");
    exit;
}

if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {
    header("Location: agregar.php?error=email");
    exit;
}


/* Valida el identificador de la cuenta seleccionada */
if (
    $usuarioId !== "" &&
    (
        !ctype_digit($usuarioId) ||
        (int) $usuarioId <= 0
    )
) {
    header("Location: agregar.php?error=cuenta");
    exit;
}

try {


/* Comprueba que la cuenta esté disponible */
if ($usuarioId !== "") {

    $consultaCuenta = $conexion->prepare(
        "SELECT u.id, u.email
         FROM usuarios AS u
         LEFT JOIN alumnos AS a
             ON a.usuario_id = u.id
         WHERE u.id = :usuario_id
           AND u.rol = 'Alumno'
           AND a.id IS NULL
         LIMIT 1"
    );

    $consultaCuenta->execute([
        ":usuario_id" => (int) $usuarioId
    ]);

    $cuenta = $consultaCuenta->fetch(PDO::FETCH_ASSOC);

    /* Verifica que exista y que coincida el correo */
    if (
        !$cuenta ||
        strtolower(trim($cuenta["email"])) !== $email
    ) {
        header("Location: agregar.php?error=cuenta");
        exit;
    }
}


/* Consulta preparada para guardar el alumno */
$consulta = $conexion->prepare(
    "INSERT INTO alumnos
        (nombre, apellido, email, usuario_id)
     VALUES
        (:nombre, :apellido, :email, :usuario_id)"
);

$consulta->execute([
    ":nombre" => $nombre,
    ":apellido" => $apellido,
    ":email" => $email,
    ":usuario_id" => $usuarioId === ""
        ? null
        : (int) $usuarioId
]);

    header("Location: index.php?mensaje=guardado");
    exit;

} catch (PDOException $error) {

 
} catch (PDOException $error) {

    /* Identifica el código de error de MySQL */
    $codigoMysql = (int) ($error->errorInfo[1] ?? 0);

    /* Error 1062: valor duplicado */
    if ($codigoMysql === 1062) {

        $detalle = $error->errorInfo[2] ?? "";

        /* Cuenta vinculada a otro alumno */
        if (str_contains($detalle, "uq_alumnos_usuario_id")) {
            header("Location: agregar.php?error=cuenta_vinculada");
            exit;
        }

        /* Correo electrónico duplicado */
        header("Location: agregar.php?error=duplicado");
        exit;
    }

    /* La cuenta seleccionada ya no existe */
    if ($codigoMysql === 1452) {
        header("Location: agregar.php?error=cuenta");
        exit;
    }

    /* Otros errores de base de datos */
    header("Location: agregar.php?error=servidor");
    exit;
}
  
