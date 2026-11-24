<?php
require_once __DIR__ . "/includes/verificar_sesion.php";

$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/includes/verificar_rol.php";
require_once "conexion.php";

/* Solo permite datos enviados por formulario */
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
    header("Location: index.php?mensaje=no_encontrado");
    exit;
}

/* Recibe y limpia los datos */
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$email = trim($_POST["email"] ?? "");

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
    header(
        "Location: editar.php?id=$id&error=campos"
    );
    exit;
}

/* Validación de longitud */
if (
    mb_strlen($nombre) < 2 ||
    mb_strlen($nombre) > 60 ||
    mb_strlen($apellido) < 2 ||
    mb_strlen($apellido) > 60
) {
    header(
        "Location: editar.php?id=$id&error=longitud"
    );
    exit;
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
    header(
        "Location: editar.php?id=$id&error=nombre"
    );
    exit;
}

/* Valida el correo */
if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {
    header(
        "Location: editar.php?id=$id&error=email"
    );
    exit;
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
        header(
            "Location: index.php?mensaje=no_encontrado"
        );
        exit;
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

    header("Location: index.php?mensaje=actualizado");
    exit;

} catch (PDOException $error) {

    /* Detecta un correo duplicado */
    if ($error->getCode() === "23000") {
        header(
            "Location: editar.php?id=$id&error=duplicado"
        );
        exit;
    }

    header(
        "Location: editar.php?id=$id&error=servidor"
    );
    exit;
}
