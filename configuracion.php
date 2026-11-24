<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
require_once "config/conexion.php";

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

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="contenido flex-grow-1">

    <div class="barra-superior">

        <button class="btn d-lg-none" id="botonMenu" aria-label="Abrir menú">
            <i class="bi bi-list fs-4"></i>
        </button>

        <h1 class="titulo-pagina"><?= e($titulo) ?></h1>

        <span>
            <i class="bi bi-person-circle"></i>
            Administrador
        </span>

    </div>

    <main class="p-3 p-md-4">

        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="tarjeta-metrica">
                    <span class="etiqueta">Base de datos</span>
                    <span class="valor fs-4"><?= e($baseDatos) ?></span>
                    <span class="variacion igual">MySQL <?= e($versionMysql) ?></span>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="tarjeta-metrica">
                    <span class="etiqueta">Versión de PHP</span>
                    <span class="valor fs-4"><?= e(PHP_VERSION) ?></span>
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

                    <div class="table-responsive">
                        <table class="table table-hover tabla-datos">
                            <thead>
                                <tr>
                                    <th>Tabla</th>
                                    <th>Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cantidades as $tabla => $cantidad): ?>
                                <tr>
                                    <td><?= e($tabla) ?></td>
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

</div>

</div> <!-- cierra el d-flex del header.php -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/scripts.js"></script>

</body>
</html>
