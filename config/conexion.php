<?php

date_default_timezone_set("America/Argentina/Jujuy");

$url = getenv("DATABASE_URL");

try {

    if ($url) {
        $datos = parse_url($url);

        $servidor = $datos["host"];
        $puerto = $datos["port"] ?? 3306;
        $usuario = rawurldecode($datos["user"] ?? "");
        $contrasena = rawurldecode($datos["pass"] ?? "");
        $baseDatos = rawurldecode(
            ltrim($datos["path"] ?? "", "/")
        );
    } else {
        // XAMPP local
        $servidor = "127.0.0.1";
        $puerto = 3307;
        $usuario = "root";
        $contrasena = "";
        $baseDatos = "instituto";
    }

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
        "Error de conexión: "
        . $error->getMessage()
    );

    die("No fue posible conectar con la base de datos.");
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