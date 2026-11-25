<?php
/*
 * header.php
 * Encabezado HTML compartido.
 */

$titulo = $titulo ?? "Sistema de Gestión Académica";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars(
            $titulo,
            ENT_QUOTES,
            "UTF-8"
        ) ?> | IES N.º 11
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Estilos propios -->
    <link
        rel="stylesheet"
        href="<?= esc(BASE_URL) ?>/assets/css/styles.css"
    >
</head>

<body>