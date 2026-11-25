<?php
$seccionRequerida = "crud_alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";

/* Solo permite datos enviados por formulario */
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
        "de edición e intentá nuevamente."
    );
}

/* Recibe el identificador */
$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
}

/* Recibe y limpia los datos */
$nombre = trim(is_string($_POST["nombre"] ?? null) ? $_POST["nombre"] : "");
$apellido = trim(is_string($_POST["apellido"] ?? null) ? $_POST["apellido"] : "");
$email = trim(is_string($_POST["email"] ?? null) ? $_POST["email"] : "");

/* Corrige mayúsculas y minúsculas */
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

/* Validación de campos obligatorios */
if (
    $nombre === "" ||
    $apellido === "" ||
    $email === ""
) {
    redirigir("editar.php", ["id" => $id, "error" => "campos"], 303);
}

/* Validación de longitud */
if (
    mb_strlen($nombre) < 2 ||
    mb_strlen($nombre) > 60 ||
    mb_strlen($apellido) < 2 ||
    mb_strlen($apellido) > 60
) {
    redirigir("editar.php", ["id" => $id, "error" => "longitud"], 303);
}

/* Solo permite letras en nombre y apellido */
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
    redirigir("editar.php", ["id" => $id, "error" => "nombre"], 303);
}

/* Valida el correo */
if (
    strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    redirigir("editar.php", ["id" => $id, "error" => "email"], 303);
}

try {

    /* Comprueba que el alumno exista */
    $buscar = $conexion->prepare(
        "SELECT id
         FROM alumnos
         WHERE id = :id"
    );

    $buscar->execute([
        ":id" => $id
    ]);

    if (!$buscar->fetch()) {
        redirigir("alumnos.php", ["mensaje" => "no_encontrado"], 303);
    }

    /* Actualiza mediante una consulta preparada */
    $consulta = $conexion->prepare(
        "UPDATE alumnos
         SET
            nombre = :nombre,
            apellido = :apellido,
            email = :email
         WHERE id = :id"
    );

    $consulta->execute([
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":email" => $email,
        ":id" => $id
    ]);

    redirigir("alumnos.php", ["mensaje" => "actualizado"], 303);

} catch (PDOException $error) {

    /* Detecta un correo duplicado */
    if ($error->getCode() === "23000") {
        redirigir("editar.php", ["id" => $id, "error" => "duplicado"], 303);
    }

    redirigir("editar.php", ["id" => $id, "error" => "servidor"], 303);
}
