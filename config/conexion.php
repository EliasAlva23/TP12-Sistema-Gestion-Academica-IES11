<?php

date_default_timezone_set("America/Argentina/Jujuy");

/*
 * LOCAL:
 * usa los valores de XAMPP.
 *
 * RAILWAY:
 * usa directamente las variables MYSQL*
 * que Railway ya inyecta en el servicio.
 */

$servidor = getenv("MYSQLHOST")
    ?: getenv("DB_HOST")
    ?: "127.0.0.1";

$puerto = getenv("MYSQLPORT")
    ?: getenv("DB_PORT")
    ?: "3307";

$baseDatos = getenv("MYSQLDATABASE")
    ?: getenv("DB_NAME")
    ?: "instituto";

$usuario = getenv("MYSQLUSER")
    ?: getenv("DB_USER")
    ?: "root";

$contrasena = getenv("MYSQLPASSWORD")
    ?: getenv("DB_PASSWORD")
    ?: "";

try {
    $conexion = new PDO(
        "mysql:host={$servidor};port={$puerto};dbname={$baseDatos};charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $error) {

    error_log(
        "Error de conexión a la base de datos: "
        . $error->getMessage()
    );

    die(
        "No fue posible conectar con la base de datos."
    );
}

if (!function_exists("e")) {
    function e(mixed $texto): string
    {
        return htmlspecialchars(
            (string) $texto,
            ENT_QUOTES,
            "UTF-8"
        );
    }
}