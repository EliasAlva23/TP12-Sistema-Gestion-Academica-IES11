<?php

if (PHP_SAPI !== "cli") {
    http_response_code(404);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

set_time_limit(0);

$direccion = "127.0.0.1";
$puerto = (int) (getenv("PORT") ?: 8080);

$servidor = stream_socket_server(
    "tcp://{$direccion}:{$puerto}",
    $errno,
    $error
);

if (!$servidor) {
    die(
        "No se pudo iniciar WebSocket: "
        . $error
        . PHP_EOL
    );
}

stream_set_blocking(
    $servidor,
    false
);

echo "========================================" . PHP_EOL;
echo " WebSocket TP11 - IES N.º 11" . PHP_EOL;
echo " ws://localhost:8080" . PHP_EOL;
echo "========================================" . PHP_EOL;

$clientes = [];

$ultimoId = (int) $conexion
    ->query(
        "SELECT COALESCE(MAX(id), 0)
         FROM notificaciones"
    )
    ->fetchColumn();


function realizarHandshake(
    $socket,
    string $solicitud
): bool {

    if (
        !preg_match(
            "/Sec-WebSocket-Key:\s*(.+)\r\n/i",
            $solicitud,
            $coincidencia
        )
    ) {
        return false;
    }

    $clave = trim(
        $coincidencia[1]
    );

    $aceptacion = base64_encode(
        pack(
            "H*",
            sha1(
                $clave
                . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"
            )
        )
    );

    $respuesta =
        "HTTP/1.1 101 Switching Protocols\r\n"
        . "Upgrade: websocket\r\n"
        . "Connection: Upgrade\r\n"
        . "Sec-WebSocket-Accept: {$aceptacion}\r\n"
        . "\r\n";

    fwrite(
        $socket,
        $respuesta
    );

    return true;
}


function crearFrame(
    string $mensaje
): string {

    $longitud =
        strlen($mensaje);

    $cabecera =
        chr(0x81);

    if ($longitud <= 125) {

        return $cabecera
            . chr($longitud)
            . $mensaje;

    }

    if ($longitud <= 65535) {

        return $cabecera
            . chr(126)
            . pack(
                "n",
                $longitud
            )
            . $mensaje;

    }

    return $cabecera
        . chr(127)
        . pack(
            "NN",
            0,
            $longitud
        )
        . $mensaje;
}


while (true) {

    /* ==============================================
       NUEVAS CONEXIONES
    ============================================== */

    $nuevoCliente =
        @stream_socket_accept(
            $servidor,
            0
        );

    if ($nuevoCliente) {

        stream_set_blocking(
            $nuevoCliente,
            false
        );

        $clientes[] = [
            "socket" => $nuevoCliente,
            "handshake" => false
        ];

        echo
            "Cliente conectado..."
            . PHP_EOL;
    }


    /* ==============================================
       HANDSHAKE WEBSOCKET
    ============================================== */

    foreach (
        $clientes
        as $indice => &$cliente
    ) {

        $socket =
            $cliente["socket"];

        if (feof($socket)) {

            fclose($socket);

            unset(
                $clientes[$indice]
            );

            continue;
        }

        if (
            !$cliente["handshake"]
        ) {

            $datos =
                @fread(
                    $socket,
                    8192
                );

            if (
                $datos !== false
                && $datos !== ""
            ) {

                if (
                    realizarHandshake(
                        $socket,
                        $datos
                    )
                ) {

                    $cliente["handshake"] =
                        true;

                    echo
                        "Handshake completado."
                        . PHP_EOL;
                }
            }
        }
    }

    unset($cliente);


    /* ==============================================
       BUSCAR NUEVAS NOTIFICACIONES
    ============================================== */

    $nuevoId = (int) $conexion->query("SELECT COALESCE(MAX(id), 0) FROM notificaciones")->fetchColumn();

    if ($nuevoId !== $ultimoId) {
        $ultimoId = $nuevoId;
        // El navegador recupera los avisos autorizados por HTTP con su sesión.
        $frame = crearFrame("actualizar");

        foreach (
            $clientes
            as $indice => $cliente
        ) {

            if (
                !$cliente["handshake"]
            ) {
                continue;
            }

            $socket =
                $cliente["socket"];

            if (feof($socket)) {

                fclose($socket);

                unset(
                    $clientes[$indice]
                );

                continue;
            }

            @fwrite(
                $socket,
                $frame
            );
        }

    }


    usleep(300000);
}
