<?php

require_once __DIR__ . "/includes/app.php";

iniciar_sesion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("login.php");
}

if (!empty($_SESSION["usuario_id"])) {
    redirigir("dashboard.php", [], 303);
}

if (!csrf_validar($_POST["csrf_login"] ?? null, "csrf_login")) {
    error_acceso(403, "La solicitud no es válida.");
}

/* Recibe y limpia los datos */
$identificador = trim(is_string($_POST["identificador"] ?? null) ? $_POST["identificador"] : "");
$contrasena = (is_string($_POST["password"] ?? null) ? $_POST["password"] : "");

/* Valida los campos obligatorios */
if ($identificador === "" || $contrasena === "") {
    redirigir("login.php", ["tipo" => "error", "mensaje" => "Completá el usuario o correo y la contraseña."], 303);
}

require_once __DIR__ . "/config/conexion.php";

try {

    /* Elimina los intentos fallidos vencidos */
    $conexion->exec(
        "DELETE FROM intentos_login
         WHERE fecha_intento < NOW() - INTERVAL 15 MINUTE"
    );

    /* Busca la cuenta por usuario o correo electrónico */
    $consulta = $conexion->prepare(
        "SELECT id, usuario, email, password, rol
         FROM usuarios
         WHERE usuario = :usuario
            OR email = :email
         LIMIT 1"
    );

    $consulta->execute([
        ":usuario" => $identificador,
        ":email" => $identificador
    ]);

    $usuarioEncontrado = $consulta->fetch(PDO::FETCH_ASSOC);

    /* Obtiene la dirección IP */
    $ip = $_SERVER["REMOTE_ADDR"] ?? "desconocida";

    /*
     * Identifica la cuenta de manera única.
     * Si existe, utiliza su ID para que el nombre
     * de usuario y el correo compartan el contador.
     */
    if ($usuarioEncontrado) {
        $claveCuenta = "cuenta:" . $usuarioEncontrado["id"];
    } else {
        $claveCuenta = "identificador:"
            . strtolower($identificador);
    }

    /* Genera una clave hash para controlar los intentos */
    $claveHash = hash(
        "sha256",
        $claveCuenta . "|ip:" . $ip
    );

    /* Cuenta los intentos fallidos de los últimos 15 minutos */
    $consultaIntentos = $conexion->prepare(
        "SELECT COUNT(*)
         FROM intentos_login
         WHERE clave_hash = :clave_hash
           AND fecha_intento >= NOW() - INTERVAL 15 MINUTE"
    );

    $consultaIntentos->execute([
        ":clave_hash" => $claveHash
    ]);

    $cantidadIntentos = (int) $consultaIntentos->fetchColumn();

    /* Bloquea temporalmente después de 5 intentos fallidos */
    if ($cantidadIntentos >= 5) {
        redirigir("login.php", ["tipo" => "error", "mensaje" => "Demasiados intentos de acceso. " . "Intentá nuevamente en unos minutos."], 303);
    }

    /* Comprueba la existencia de la cuenta y su contraseña */
    if (
        !$usuarioEncontrado ||
        !password_verify(
            $contrasena,
            $usuarioEncontrado["password"]
        )
    ) {

        /* Registra el intento fallido */
        $registrarIntento = $conexion->prepare(
            "INSERT INTO intentos_login (clave_hash)
             VALUES (:clave_hash)"
        );

        $registrarIntento->execute([
            ":clave_hash" => $claveHash
        ]);

        redirigir("login.php", ["tipo" => "error", "mensaje" => "Usuario, correo o contraseña incorrectos."], 303);
    }

    /* Limpia los intentos después de un acceso correcto */
    $limpiarIntentos = $conexion->prepare(
        "DELETE FROM intentos_login
         WHERE clave_hash = :clave_hash"
    );

    $limpiarIntentos->execute([
        ":clave_hash" => $claveHash
    ]);

    /* Renueva el identificador de sesión */
    session_regenerate_id(true);

    $_SESSION = [
        "usuario_id" => $usuarioEncontrado["id"],
        "usuario" => $usuarioEncontrado["usuario"],
        "email" => $usuarioEncontrado["email"],
        "rol" => $usuarioEncontrado["rol"],
        "ultima_actividad" => time(),
    ];

    redirigir("dashboard.php", [], 303);

} catch (PDOException $error) {
    error_log("Inicio de sesión: " . $error->getMessage());

    redirigir("login.php", ["tipo" => "error", "mensaje" => "No fue posible iniciar sesión. " . "Intentá nuevamente."], 303);
}
