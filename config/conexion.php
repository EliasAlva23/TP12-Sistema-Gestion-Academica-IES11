<?php

date_default_timezone_set(
    "America/Argentina/Buenos_Aires"
);

$servidor = "127.0.0.1";
$puerto = 3307;

$baseDatos = "instituto";

$usuario = "root";
$contrasena = "";

try {

    $conexion = new PDO(
        "mysql:host=$servidor;" .
        "port=$puerto;" .
        "dbname=$baseDatos;" .
        "charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $error) {

    die(
        "No se pudo conectar con la base de datos: " .
        $error->getMessage()
    );
}


function e($texto): string
{
    return htmlspecialchars(
        (string) $texto,
        ENT_QUOTES,
        "UTF-8"
    );
}