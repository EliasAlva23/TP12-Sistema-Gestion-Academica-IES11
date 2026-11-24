<?php
require_once __DIR__ . "/includes/verificar_sesion.php";

require_once "config/conexion.php";

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


include "includes/header.php";
include "includes/sidebar.php";

?>

<div class="contenido flex-grow-1">

    <div class="barra-superior">

        <button
            class="btn d-lg-none"
            id="botonMenu"
            aria-label="Abrir menú"
        >
            <i class="bi bi-list fs-4"></i>
        </button>


        <h1 class="titulo-pagina">
            <?= e($titulo) ?>
        </h1>


        <span>
            <i class="bi bi-building"></i>
            IES N.º 11
        </span>

    </div>


    <main class="p-3 p-md-4">


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

        <div class="row g-4 mt-1">


            <div class="col-12 col-lg-8">

                <div class="tarjeta-panel">

                    <h2>
                        Altas de alumnos
                        — últimos 30 días
                    </h2>

                    <div class="grafico">

                        <canvas
                            id="reporteAltasChart"
                        ></canvas>

                    </div>

                </div>

            </div>


            <div class="col-12 col-lg-4">

                <div class="tarjeta-panel">

                    <h2>
                        Distribución
                        de usuarios por rol
                    </h2>

                    <div class="grafico">

                        <canvas
                            id="reporteRolesChart"
                        ></canvas>

                    </div>

                </div>

            </div>

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


                    <div class="table-responsive">

                        <table
                            class="
                                table
                                table-hover
                                tabla-datos
                            "
                        >

                            <thead>

                                <tr>

                                    <th>ID</th>

                                    <th>Usuario</th>

                                    <th>Correo</th>

                                    <th>Rol</th>

                                    <th>Fecha</th>

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
                                        <?= e(
                                            $usuario[
                                                "usuario"
                                            ]
                                        ) ?>
                                    </td>


                                    <td>
                                        <?= e(
                                            $usuario[
                                                "email"
                                            ]
                                        ) ?>
                                    </td>


                                    <td>
                                        <?= e(
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

</div>


</div>


<script
    src="
    https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script
    src="
    https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"
></script>


<script>

const reporteAcademico =

<?= json_encode(

    [

        "altas" => [

            "etiquetas" =>
                $etiquetasAltas,

            "valores" =>
                $valoresAltas

        ],

        "roles" => [

            "etiquetas" =>
                $etiquetasRoles,

            "valores" =>
                $valoresRoles

        ]

    ],

    JSON_UNESCAPED_UNICODE

) ?>;


const altasCanvas =
    document.getElementById(
        "reporteAltasChart"
    );


if (altasCanvas) {

    new Chart(
        altasCanvas,
        {

            type: "bar",

            data: {

                labels:
                    reporteAcademico
                        .altas
                        .etiquetas,

                datasets: [

                    {

                        label:
                            "Alumnos registrados",

                        data:
                            reporteAcademico
                                .altas
                                .valores,

                        backgroundColor:
                            "#4c78c2",

                        borderRadius:
                            4

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio:
                    false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }

                    }

                }

            }

        }

    );

}


const rolesCanvas =
    document.getElementById(
        "reporteRolesChart"
    );


if (rolesCanvas) {

    new Chart(
        rolesCanvas,
        {

            type: "doughnut",

            data: {

                labels:
                    reporteAcademico
                        .roles
                        .etiquetas,

                datasets: [

                    {

                        data:
                            reporteAcademico
                                .roles
                                .valores,

                        backgroundColor: [

                            "#2c374e",

                            "#4c78c2",

                            "#36afd3",

                            "#e9a62f"

                        ],

                        borderColor:
                            "#ffffff",

                        borderWidth:
                            2

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio:
                    false,

                plugins: {

                    legend: {

                        position:
                            "bottom"

                    }

                }

            }

        }

    );

}

</script>


<script
    src="assets/js/scripts.js"
></script>


</body>

</html>