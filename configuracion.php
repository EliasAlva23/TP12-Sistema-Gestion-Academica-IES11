<?php

$seccionRequerida = "configuracion";
require_once __DIR__ . "/includes/verificar_rol.php";

$titulo = "Configuración";
$pagina = "configuracion";

// Cantidad de registros de cada tabla (los nombres los escribimos nosotros)
$tablas = [
    "alumnos",
    "usuarios"
];

foreach ($tablas as $tabla) {
    $cantidades[$tabla] = (int) $conexion
        ->query("SELECT COUNT(*) FROM $tabla")
        ->fetchColumn();
}

$baseDatos = $conexion->query("SELECT DATABASE()")->fetchColumn();
$versionMysql = $conexion->getAttribute(PDO::ATTR_SERVER_VERSION);

require __DIR__ . "/includes/header.php";
?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1><?= esc($titulo) ?></h1>
        <p>Revisá el entorno del sistema y los registros de la base de datos.</p>
    </header>

        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="tarjeta-metrica">
                    <span class="etiqueta">Base de datos</span>
                    <span class="valor fs-4"><?= esc($baseDatos) ?></span>
                    <span class="variacion igual">MySQL <?= esc($versionMysql) ?></span>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="tarjeta-metrica">
                    <span class="etiqueta">Versión de PHP</span>
                    <span class="valor fs-4"><?= esc(PHP_VERSION) ?></span>
                    <span class="variacion igual">servidor local</span>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="tarjeta-metrica">
                    <span class="etiqueta">Fecha del servidor</span>
                    <span class="valor fs-4"><?= date("d/m/Y") ?></span>
                    <span class="variacion igual"><?= date("H:i") ?> hs</span>
                </div>
            </div>

        </div>

        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="tarjeta-panel">

                    <h2>Registros por tabla</h2>

                    <div class="table-responsive" tabindex="0" role="region" aria-label="Registros por tabla">
                        <table class="table table-hover tabla-datos">
                            <thead>
                                <tr>
                                    <th scope="col">Tabla</th>
                                    <th scope="col">Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cantidades as $tabla => $cantidad): ?>
                                <tr>
                                    <td><?= esc($tabla) ?></td>
                                    <td><?= $cantidad ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </main>

</body>
</html>
