<?php
$seccionRequerida = "reportes";
require_once __DIR__ . "/includes/verificar_rol.php";

$titulo = "Reportes académicos";
$pagina = "reportes";


/* ==================================================
   RESUMEN GENERAL
================================================== */

$totalAlumnos = (int) $conexion
    ->query(
        "SELECT COUNT(*)
         FROM alumnos"
    )
    ->fetchColumn();


$totalUsuarios = (int) $conexion
    ->query(
        "SELECT COUNT(*)
         FROM usuarios"
    )
    ->fetchColumn();


$totalDocentes = (int) $conexion
    ->query(
        "SELECT COUNT(*)
         FROM usuarios
         WHERE rol = 'Docente'"
    )
    ->fetchColumn();


$totalCuentasAlumno = (int) $conexion
    ->query(
        "SELECT COUNT(*)
         FROM usuarios
         WHERE rol = 'Alumno'"
    )
    ->fetchColumn();


/* ==================================================
   USUARIOS POR ROL
================================================== */

$usuariosPorRol = $conexion
    ->query(
        "SELECT
            rol,
            COUNT(*) AS total
         FROM usuarios
         GROUP BY rol
         ORDER BY rol"
    )
    ->fetchAll(
        PDO::FETCH_KEY_PAIR
    );


$roles = [
    "Administrador",
    "Docente",
    "Alumno",
    "Invitado"
];


$etiquetasRoles = [];
$valoresRoles = [];


foreach ($roles as $rol) {

    $etiquetasRoles[] =
        $rol;

    $valoresRoles[] =
        (int) (
            $usuariosPorRol[$rol]
            ?? 0
        );
}


/* ==================================================
   ALTAS DE ALUMNOS ÚLTIMOS 30 DÍAS
================================================== */

$altas = $conexion
    ->query(
        "SELECT
            DATE(fecha_registro)
                AS fecha,
            COUNT(*)
                AS total
         FROM alumnos
         WHERE fecha_registro >=
            DATE_SUB(
                CURDATE(),
                INTERVAL 29 DAY
            )
         GROUP BY
            DATE(fecha_registro)
         ORDER BY
            fecha ASC"
    )
    ->fetchAll(
        PDO::FETCH_KEY_PAIR
    );


$etiquetasAltas = [];
$valoresAltas = [];


for ($i = 29; $i >= 0; $i--) {

    $marca =
        strtotime("-$i days");

    $fecha =
        date(
            "Y-m-d",
            $marca
        );

    $etiquetasAltas[] =
        date(
            "d/m",
            $marca
        );

    $valoresAltas[] =
        (int) (
            $altas[$fecha]
            ?? 0
        );
}


/* ==================================================
   ÚLTIMOS USUARIOS
================================================== */

$ultimosUsuarios = $conexion
    ->query(
        "SELECT
            id,
            usuario,
            email,
            rol,
            fecha_registro
         FROM usuarios
         ORDER BY
            fecha_registro DESC,
            id DESC
         LIMIT 5"
    )
    ->fetchAll();


require __DIR__ . "/includes/header.php";

?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1><?= esc($titulo) ?></h1>
        <p>Consultá las altas de alumnos y la distribución de usuarios por rol.</p>
    </header>


        <!-- ==========================================
             RESUMEN
        =========================================== -->

        <div class="row g-4">


            <div class="col-12 col-sm-6 col-xl-3">

                <div class="tarjeta-metrica">

                    <span class="etiqueta">
                        Alumnos
                    </span>

                    <span class="valor">
                        <?= $totalAlumnos ?>
                    </span>

                    <span class="variacion igual">
                        registrados
                    </span>

                </div>

            </div>


            <div class="col-12 col-sm-6 col-xl-3">

                <div class="tarjeta-metrica">

                    <span class="etiqueta">
                        Usuarios
                    </span>

                    <span class="valor">
                        <?= $totalUsuarios ?>
                    </span>

                    <span class="variacion igual">
                        cuentas
                    </span>

                </div>

            </div>


            <div class="col-12 col-sm-6 col-xl-3">

                <div class="tarjeta-metrica">

                    <span class="etiqueta">
                        Docentes
                    </span>

                    <span class="valor">
                        <?= $totalDocentes ?>
                    </span>

                    <span class="variacion igual">
                        cuentas
                    </span>

                </div>

            </div>


            <div class="col-12 col-sm-6 col-xl-3">

                <div class="tarjeta-metrica">

                    <span class="etiqueta">
                        Cuentas de alumnos
                    </span>

                    <span class="valor">
                        <?= $totalCuentasAlumno ?>
                    </span>

                    <span class="variacion igual">
                        usuarios
                    </span>

                </div>

            </div>

        </div>


        <!-- ==========================================
             GRÁFICOS
        =========================================== -->

        <div class="rejilla-graficos my-4">
            <?php
            $idGrafico = "reporteAltasChart";
            $tituloGrafico = "Altas de alumnos · últimos 30 días";
            $tipoGrafico = "bar";
            $etiquetasGrafico = $etiquetasAltas;
            $valoresGrafico = $valoresAltas;
            require __DIR__ . "/includes/grafico.php";
            ?>

            <?php
            $idGrafico = "reporteRolesChart";
            $tituloGrafico = "Distribución de usuarios por rol";
            $tipoGrafico = "doughnut";
            $etiquetasGrafico = $etiquetasRoles;
            $valoresGrafico = $valoresRoles;
            require __DIR__ . "/includes/grafico.php";
            ?>
        </div>

        <!-- ==========================================
             ÚLTIMOS USUARIOS
        =========================================== -->

        <div class="row g-4 mt-1">

            <div class="col-12">

                <div class="tarjeta-panel">

                    <h2>
                        Últimos usuarios registrados
                    </h2>


                    <div class="table-responsive" tabindex="0" role="region" aria-label="Últimos usuarios registrados">

                        <table
                            class="
                                table
                                table-hover
                                tabla-datos tabla-registros
                            "
                        >

                            <thead>

                                <tr>

                                    <th scope="col">ID</th>

                                    <th scope="col">Usuario</th>

                                    <th scope="col">Correo</th>

                                    <th scope="col">Rol</th>

                                    <th scope="col">Fecha</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php foreach (
                                $ultimosUsuarios
                                as $usuario
                            ): ?>


                                <tr>

                                    <td>
                                        #<?= $usuario["id"] ?>
                                    </td>


                                    <td>
                                        <?= esc(
                                            $usuario[
                                                "usuario"
                                            ]
                                        ) ?>
                                    </td>


                                    <td>
                                        <?= esc(
                                            $usuario[
                                                "email"
                                            ]
                                        ) ?>
                                    </td>


                                    <td>
                                        <?= esc(
                                            $usuario[
                                                "rol"
                                            ]
                                        ) ?>
                                    </td>


                                    <td>

                                        <?= date(
                                            "d/m/Y H:i",
                                            strtotime(
                                                $usuario[
                                                    "fecha_registro"
                                                ]
                                            )
                                        ) ?>

                                    </td>

                                </tr>


                            <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


    </main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>
<script src="<?= esc(url_app("assets/js/charts.js")) ?>" defer></script>


</body>

</html>
