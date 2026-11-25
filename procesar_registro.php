<?php

require_once __DIR__ . "/includes/app.php";

iniciar_sesion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("registro.php");
}

if (!empty($_SESSION["usuario_id"])) {
    redirigir("dashboard.php", [], 303);
}

if (!csrf_validar($_POST["csrf_registro"] ?? null, "csrf_registro")) {
    error_acceso(403, "La solicitud no es válida.");
}

/* Recibir y limpiar los datos */
$usuario = trim(is_string($_POST["usuario"] ?? null) ? $_POST["usuario"] : "");
$email = trim(is_string($_POST["email"] ?? null) ? $_POST["email"] : "");
$rol = trim(is_string($_POST["rol"] ?? null) ? $_POST["rol"] : "");
$password = (is_string($_POST["password"] ?? null) ? $_POST["password"] : "");
$confirmarPassword = is_string($_POST["confirmar_password"] ?? null) ? $_POST["confirmar_password"] : "";
$terminos = $_POST["terminos"] ?? "";

/* Función para volver al formulario con un mensaje */
function volverAlRegistro(
    string $mensaje,
    string $tipo = "error"
): void {
    redirigir("registro.php", ["mensaje" => $mensaje, "tipo" => $tipo], 303);
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
if (strlen($email) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

require_once __DIR__ . "/config/conexion.php";

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
    error_log("Registro: " . $error->getMessage());
    /*
     * No se muestra el detalle técnico del error
     * para evitar exponer información del sistema.
     */
    volverAlRegistro(
        "No fue posible crear la cuenta. "
        . "Intentá nuevamente."
    );
}
