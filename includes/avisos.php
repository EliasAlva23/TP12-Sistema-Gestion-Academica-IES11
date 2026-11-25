<?php

function avisos_visibles(PDO $conexion, int $usuarioId, string $rol, bool $soloRecientes = false): array
{
    $sql = "SELECT titulo, mensaje, fecha_creacion
            FROM notificaciones
            WHERE (
                usuario_id = :usuario_id
                OR (usuario_id IS NULL AND (rol_destino IS NULL OR rol_destino = :rol))
            )";

    if ($soloRecientes) {
        $sql .= " AND leida = 0";
    }

    $sql .= " ORDER BY fecha_creacion DESC, id DESC";
    if ($soloRecientes) {
        $sql .= " LIMIT 6";
    }

    $consulta = $conexion->prepare($sql);
    $consulta->execute([":usuario_id" => $usuarioId, ":rol" => $rol]);

    return $consulta->fetchAll();
}
