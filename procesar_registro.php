<?php

/* Inicia la sesión para verificar el token CSRF */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "conexion.php";

/* Solo permite solicitudes enviadas desde el formulario */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.php");
    exit;
}

/* Verifica el token CSRF del registro público */
$tokenCsrf = $_POST["csrf_registro"] ?? null;

if (
    !is_string($tokenCsrf) ||
    empty($_SESSION["csrf_registro"]) ||
    !hash_equals(
        $_SESSION["csrf_registro"],
        $tokenCsrf
    )
) {
    http_response_code(403);

    exit(
        "Solicitud no válida. Volvé al formulario " .
        "de registro e intentá nuevamente."
    );
}

/* Recibir y limpiar los datos */
$usuario = trim($_POST["usuario"] ?? "");
$email = trim($_POST["email"] ?? "");
$rol = trim($_POST["rol"] ?? "");
$password = $_POST["password"] ?? "";
$confirmarPassword =
    $_POST["confirmar_password"] ?? "";
$terminos = $_POST["terminos"] ?? "";

/* Función para volver al formulario con un mensaje */
function volverAlRegistro(
    string $mensaje,
    string $tipo = "error"
): void {
    $parametros = http_build_query([
        "mensaje" => $mensaje,
        "tipo" => $tipo
    ]);

    header("Location: registro.php?" . $parametros);
    exit;
}

/* Validar campos obligatorios */
if (
    $usuario === "" ||
    $email === "" ||
    $rol === "" ||
    $password === "" ||
    $confirmarPassword === ""
) {
    volverAlRegistro(
        "Completá todos los campos obligatorios."
    );
}

/* Validar nombre de usuario */
if (
    !preg_match(
        "/^[a-zA-Z0-9._-]{4,50}$/",
        $usuario
    )
) {
    volverAlRegistro(
        "El usuario debe tener entre 4 y 50 caracteres. "
        . "Solo puede contener letras, números, puntos, "
        . "guiones y guiones bajos."
    );
}

/* Normalizar usuario y correo */
$usuario = strtolower($usuario);
$email = strtolower($email);

/* Validar correo electrónico */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    volverAlRegistro(
        "Ingresá un correo electrónico válido."
    );
}

/* Solo se permiten roles de registro público */
$rolesPermitidos = [
    "Alumno",
    "Invitado"
];

if (!in_array($rol, $rolesPermitidos, true)) {
    volverAlRegistro(
        "El tipo de perfil seleccionado no es válido."
    );
}

/* Validar extensión de la contraseña */
$longitudPassword = strlen($password);

if (
    $longitudPassword < 8 ||
    $longitudPassword > 72
) {
    volverAlRegistro(
        "La contraseña debe tener entre 8 y 72 caracteres."
    );
}

/* Exigir al menos una letra y un número */
if (
    !preg_match("/[A-Za-z]/", $password) ||
    !preg_match("/[0-9]/", $password)
) {
    volverAlRegistro(
        "La contraseña debe incluir al menos una letra "
        . "y un número."
    );
}

/* Confirmar que las contraseñas sean iguales */
if ($password !== $confirmarPassword) {
    volverAlRegistro(
        "Las contraseñas no coinciden."
    );
}

/* Comprobar aceptación de condiciones */
if ($terminos !== "1") {
    volverAlRegistro(
        "Debés aceptar las condiciones de uso."
    );
}

try {
    /*
     * Comprobar si el usuario o correo ya están registrados.
     * Se utiliza una consulta preparada para evitar
     * inyección SQL.
     */
    $consultaExistente = $conexion->prepare(
        "SELECT id
         FROM usuarios
         WHERE usuario = :usuario
            OR email = :email
         LIMIT 1"
    );

    $consultaExistente->execute([
        ":usuario" => $usuario,
        ":email" => $email
    ]);

    if ($consultaExistente->fetch()) {
        volverAlRegistro(
            "El nombre de usuario o el correo electrónico "
            . "ya se encuentran registrados."
        );
    }

    /*
     * La contraseña se protege antes de almacenarla.
     * Nunca se guarda la contraseña original.
     */
    $passwordProtegida = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $consulta = $conexion->prepare(
        "INSERT INTO usuarios (
            usuario,
            email,
            password,
            rol
        ) VALUES (
            :usuario,
            :email,
            :password,
            :rol
        )"
    );

    $consulta->execute([
        ":usuario" => $usuario,
        ":email" => $email,
        ":password" => $passwordProtegida,
        ":rol" => $rol
    ]);

    volverAlRegistro(
        "La cuenta fue creada correctamente. "
        . "Ya podés iniciar sesión.",
        "exito"
    );

} catch (PDOException $error) {
    /*
     * No se muestra el detalle técnico del error
     * para evitar exponer información del sistema.
     */
    volverAlRegistro(
        "No fue posible crear la cuenta. "
        . "Intentá nuevamente."
    );
}
