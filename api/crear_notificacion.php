<?php

header(
    "Content-Type: application/json; charset=utf-8"
);

require_once __DIR__
    . "/../config/conexion.php";


$datos =
    json_decode(
        file_get_contents(
            "php://input"
        ),
        true
    );


if (!is_array($datos)) {

    $datos = $_POST;
}


$tipo =
    trim(
        $datos["tipo"]
        ?? "sistema"
    );


$titulo =
    trim(
        $datos["titulo"]
        ?? ""
    );


$mensaje =
    trim(
        $datos["mensaje"]
        ?? ""
    );


$rolDestino =
    trim(
        $datos["rol_destino"]
        ?? ""
    );


if (
    $titulo === ""
    || $mensaje === ""
) {

    http_response_code(400);

    echo json_encode(
        [
            "ok" => false,
            "mensaje" =>
                "Título y mensaje son obligatorios."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


$consulta =
    $conexion->prepare(
        "INSERT INTO notificaciones (
            tipo,
            titulo,
            mensaje,
            rol_destino
        )
        VALUES (
            :tipo,
            :titulo,
            :mensaje,
            :rol_destino
        )"
    );


$consulta->execute([
    ":tipo" => $tipo,
    ":titulo" => $titulo,
    ":mensaje" => $mensaje,
    ":rol_destino" =>
        $rolDestino !== ""
            ? $rolDestino
            : null
]);


echo json_encode(
    [
        "ok" => true,
        "id" =>
            $conexion->lastInsertId(),

        "mensaje" =>
            "Notificación creada correctamente."
    ],
    JSON_UNESCAPED_UNICODE
);