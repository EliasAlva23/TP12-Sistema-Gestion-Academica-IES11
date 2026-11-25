<?php

require_once __DIR__ . "/../includes/app.php";

date_default_timezone_set("America/Argentina/Jujuy");

try {
    $conexion = (static function (): PDO {
        $url = getenv("DATABASE_URL");

        if ($url !== false && $url !== "") {
            $datos = parse_url($url);
            if ($datos === false
                || !in_array($datos["scheme"] ?? "", ["mysql", "mariadb"], true)
                || empty($datos["host"])
                || empty($datos["user"])
                || ltrim($datos["path"] ?? "", "/") === ""
            ) {
                throw new RuntimeException("DATABASE_URL tiene un formato inválido.");
            }

            $servidor = $datos["host"];
            $puerto = $datos["port"] ?? 3306;
            $usuario = rawurldecode($datos["user"]);
            $contrasena = rawurldecode($datos["pass"] ?? "");
            $baseDatos = rawurldecode(ltrim($datos["path"], "/"));
        } else {
            $servidor = "127.0.0.1";
            $puerto = 3307;
            $usuario = "root";
            $contrasena = "";
            $baseDatos = "instituto";
        }

        return new PDO(
            "mysql:host={$servidor};port={$puerto};dbname={$baseDatos};charset=utf8mb4",
            $usuario,
            $contrasena,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    })();
} catch (PDOException | RuntimeException $error) {
    error_log("Conexión a base de datos: " . $error->getMessage());
    if (PHP_SAPI === "cli") {
        exit(1);
    }

    error_acceso(503, "No fue posible conectar con la base de datos.");
}
