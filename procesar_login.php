
<?php

session_start();

require_once __DIR__ . "/config/conexion.php";

/* Solo permite solicitudes enviadas desde el formulario */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

/* Verifica el token CSRF del inicio de sesión */
$tokenCsrf = $_POST["csrf_login"] ?? null;

if (
    !is_string($tokenCsrf) ||
    empty($_SESSION["csrf_login"]) ||
    !hash_equals(
        $_SESSION["csrf_login"],
        $tokenCsrf
    )
) {
    http_response_code(403);

    exit(
        "Solicitud no válida. Volvé al formulario " .
        "de inicio de sesión e intentá nuevamente."
    );
}

/* Recibe y limpia los datos */
$identificador = trim($_POST["identificador"] ?? "");
$contrasena = $_POST["password"] ?? "";

/* Valida los campos obligatorios */
if ($identificador === "" || $contrasena === "") {
    header(
        "Location: login.php?tipo=error&mensaje="
        . urlencode(
            "Completá el usuario o correo y la contraseña."
        )
    );
    exit;
}

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
        header(
            "Location: login.php?tipo=error&mensaje="
            . urlencode(
                "Demasiados intentos de acceso. "
                . "Intentá nuevamente en unos minutos."
            )
        );
        exit;
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

        header(
            "Location: login.php?tipo=error&mensaje="
            . urlencode(
                "Usuario, correo o contraseña incorrectos."
            )
        );
        exit;
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

    /* Guarda los datos del usuario en la sesión */
    $_SESSION["usuario_id"] = $usuarioEncontrado["id"];

    $_SESSION["usuario"] = $usuarioEncontrado["usuario"];

    $_SESSION["email"] = $usuarioEncontrado["email"];

    $_SESSION["rol"] = $usuarioEncontrado["rol"];

    /* Redirige al panel correspondiente */
    header("Location: dashboard.php");
    exit;

} catch (PDOException $error) {

    header(
        "Location: login.php?tipo=error&mensaje="
        . urlencode(
            "No fue posible iniciar sesión. "
            . "Intentá nuevamente."
        )
    );

    exit;
}