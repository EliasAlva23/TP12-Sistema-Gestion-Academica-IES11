<?php

$titulo = $titulo ?? "Sistema de Gestión Académica";
$paginaPublica = $paginaPublica ?? false;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titulo) ?> | IES N.º 11</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= esc(url_app("assets/css/styles.css")) ?>">
    <?php if (!$paginaPublica): ?>
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif; ?>
</head>
<body<?= $paginaPublica ? ' class="pagina-acceso"' : "" ?>>
<a class="visually-hidden-focusable salto-contenido" href="#contenido-principal">Saltar al contenido</a>
<?php if (!$paginaPublica) { require __DIR__ . "/navbar.php"; } ?>
