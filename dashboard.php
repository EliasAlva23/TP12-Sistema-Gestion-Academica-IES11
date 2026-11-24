<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
require_once __DIR__ . "/config/conexion.php";

if (!function_exists("esc")) {
    function esc(mixed $valor): string {
        return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
    }
}

/* ==================================================
   SESIÓN Y ROLES
================================================== */

$rol = $_SESSION["rol"] ?? "";
$usuarioSesion = $_SESSION["usuario"] ?? "";
$emailSesion = $_SESSION["email"] ?? "";

$rolesPermitidos = [
    "Administrador",
    "Docente",
    "Alumno",
    "Invitado"
];

if (!in_array($rol, $rolesPermitidos, true)) {
    header("Location: logout.php");
    exit;
}

$mensaje = $_GET["mensaje"] ?? "";
$tipo = $_GET["tipo"] ?? "";

$descripciones = [
    "Administrador" => "Tenés acceso completo a alumnos, usuarios, reportes, notificaciones y configuración.",
    "Docente" => "Podés consultar información académica y el listado de alumnos.",
    "Alumno" => "Podés consultar tu información académica personal.",
    "Invitado" => "Tenés acceso limitado a la información general del sistema."
];

$panelesRol = [
    "Administrador" => "roles/administrador.php",
    "Docente" => "roles/docente.php",
    "Alumno" => "roles/alumno.php",
    "Invitado" => "roles/invitado.php"
];


/* ==================================================
   ACCESOS SEGÚN ROL
================================================== */

$accesos = [
    [
        "icono" => "bi-person-workspace",
        "titulo" => "Mi panel",
        "texto" => "Accedé a las funciones específicas de tu perfil.",
        "enlace" => $panelesRol[$rol]
    ]
];

if ($rol === "Administrador") {
    $accesos = array_merge($accesos, [
        [
            "icono" => "bi-mortarboard",
            "titulo" => "Gestión de alumnos",
            "texto" => "Consultar y administrar alumnos registrados.",
            "enlace" => "alumnos.php"
        ],
        [
            "icono" => "bi-people",
            "titulo" => "Usuarios",
            "texto" => "Consultar cuentas y roles del sistema.",
            "enlace" => "usuarios.php"
        ],
        [
            "icono" => "bi-bar-chart",
            "titulo" => "Reportes",
            "texto" => "Consultar información estadística académica.",
            "enlace" => "reportes.php"
        ],
        [
            "icono" => "bi-bell",
            "titulo" => "Notificaciones",
            "texto" => "Consultar los avisos registrados.",
            "enlace" => "notificaciones.php"
        ]
    ]);
}

if ($rol === "Docente") {
    $accesos[] = [
        "icono" => "bi-mortarboard",
        "titulo" => "Listado de alumnos",
        "texto" => "Consultar alumnos registrados.",
        "enlace" => "alumnos.php"
    ];

    $accesos[] = [
        "icono" => "bi-bell",
        "titulo" => "Notificaciones",
        "texto" => "Consultar avisos académicos.",
        "enlace" => "notificaciones.php"
    ];
}

if ($rol === "Alumno") {
    $accesos[] = [
        "icono" => "bi-bell",
        "titulo" => "Notificaciones",
        "texto" => "Consultar avisos del sistema académico.",
        "enlace" => "notificaciones.php"
    ];
}


/* ==================================================
   ESTADÍSTICAS
================================================== */

$puedeVerEstadisticas = in_array(
    $rol,
    ["Administrador", "Docente"],
    true
);

$totalAlumnos = null;
$totalUsuarios = null;
$totalDocentes = null;
$altasRecientes = null;

$ultimosAlumnos = [];
$datosAltas = [];
$datosRoles = [];
$errorEstadisticas = false;

if ($puedeVerEstadisticas) {
    try {
        $totalAlumnos = (int) $conexion
            ->query("SELECT COUNT(*) FROM alumnos")
            ->fetchColumn();

        $altasRecientes = (int) $conexion
            ->query(
                "SELECT COUNT(*)
                 FROM alumnos
                 WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            )
            ->fetchColumn();

        $ultimosAlumnos = $conexion
            ->query(
                "SELECT id, nombre, apellido, email, fecha_registro
                 FROM alumnos
                 ORDER BY fecha_registro DESC, id DESC
                 LIMIT 5"
            )
            ->fetchAll(PDO::FETCH_ASSOC);

        /* Altas últimos 7 días */
        $altasBD = $conexion
            ->query(
                "SELECT DATE(fecha_registro) AS fecha, COUNT(*) AS total
                 FROM alumnos
                 WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                 GROUP BY DATE(fecha_registro)"
            )
            ->fetchAll(PDO::FETCH_KEY_PAIR);

        $dias = [
            1 => "Lun",
            2 => "Mar",
            3 => "Mié",
            4 => "Jue",
            5 => "Vie",
            6 => "Sáb",
            7 => "Dom"
        ];

        for ($i = 6; $i >= 0; $i--) {
            $marca = strtotime("-$i days");
            $fecha = date("Y-m-d", $marca);

            $datosAltas[] = [
                "etiqueta" => $dias[(int) date("N", $marca)],
                "cantidad" => (int) ($altasBD[$fecha] ?? 0)
            ];
        }

        /* Estadísticas exclusivas del administrador */
        if ($rol === "Administrador") {
            $totalUsuarios = (int) $conexion
                ->query("SELECT COUNT(*) FROM usuarios")
                ->fetchColumn();

            $rolesBD = $conexion
                ->query(
                    "SELECT rol, COUNT(*) AS total
                     FROM usuarios
                     GROUP BY rol"
                )
                ->fetchAll(PDO::FETCH_KEY_PAIR);

            $totalDocentes = (int) ($rolesBD["Docente"] ?? 0);

            foreach (
                ["Administrador", "Docente", "Alumno", "Invitado"]
                as $nombreRol
            ) {
                $datosRoles[] = [
                    "etiqueta" => $nombreRol,
                    "cantidad" => (int) ($rolesBD[$nombreRol] ?? 0)
                ];
            }
        }

    } catch (PDOException $error) {
        $errorEstadisticas = true;
        error_log("Error dashboard: " . $error->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard académico | IES N.º 11</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/estilos.css"
    >

    <style>
        body {
            background: #f4f7fb;
        }

        .dashboard-contenedor {
            max-width: 1400px;
            margin: auto;
        }

        .dashboard-cabecera {
            background: linear-gradient(135deg, #253e66, #4774bf);
            color: #fff;
            padding: 2rem 0;
        }

        .dashboard-cabecera h1 {
            font-weight: 700;
        }

        .dashboard-panel,
        .metrica-dashboard,
        .acceso-dashboard {
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(20, 50, 90, .05);
        }

        .dashboard-panel {
            padding: 1.25rem;
        }

        .dashboard-panel h2 {
            color: #253e66;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .dashboard-accesos {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .acceso-dashboard {
            display: block;
            padding: 1.25rem;
            color: inherit;
            text-decoration: none;
            transition: .2s ease;
        }

        .acceso-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(20, 50, 90, .1);
        }

        .acceso-dashboard i {
            color: #4774bf;
            font-size: 1.6rem;
        }

        .acceso-dashboard h3 {
            margin-top: .8rem;
            font-size: 1rem;
            color: #253e66;
        }

        .acceso-dashboard p {
            margin: 0;
            color: #687992;
            font-size: .9rem;
        }

        .dashboard-metricas {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metrica-dashboard {
            padding: 1.25rem;
        }

        .metrica-dashboard i {
            color: #4774bf;
            font-size: 1.5rem;
        }

        .metrica-dashboard strong {
            display: block;
            margin-top: .5rem;
            font-size: 1.8rem;
            color: #253e66;
        }

        .metrica-dashboard span {
            color: #687992;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .dashboard-grafico {
            position: relative;
            height: 300px;
        }

        .panel-notificaciones {
            position: absolute;
            top: 55px;
            right: 0;
            width: min(360px, 90vw);
            padding: 1rem;
            background: #fff;
            color: #253e66;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .18);
            z-index: 2000;
        }

        @media (max-width: 992px) {
            .dashboard-metricas {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .dashboard-metricas {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<?php require __DIR__ . "/includes/navbar.php"; ?>

<header class="dashboard-cabecera">
    <div class="container dashboard-contenedor">

        <div class="d-flex justify-content-between align-items-center gap-3">

            <div>
                <small class="text-uppercase">
                    Sistema de Gestión Académica
                </small>

                <h1 class="mt-1 mb-1">
                    Hola, <?= esc($usuarioSesion) ?>
                </h1>

                <p class="mb-0">
                    Sesión iniciada como
                    <strong><?= esc($rol) ?></strong>
                </p>
            </div>


            <!-- CAMPANA DE NOTIFICACIONES -->

            <div class="position-relative">

                <button
                    type="button"
                    id="botonNotificaciones"
                    class="btn btn-light position-relative"
                    aria-label="Ver notificaciones"
                >
                    <i class="bi bi-bell"></i>

                    <span
                        id="contadorNotificaciones"
                        class="
                            position-absolute
                            top-0
                            start-100
                            translate-middle
                            badge
                            rounded-pill
                            bg-danger
                            d-none
                        "
                    >
                        0
                    </span>
                </button>


                <div
                    id="panelNotificaciones"
                    class="panel-notificaciones d-none"
                >

                    <div
                        class="
                            d-flex
                            justify-content-between
                            mb-3
                        "
                    >
                        <strong>Notificaciones</strong>

                        <a href="notificaciones.php">
                            Ver todas
                        </a>
                    </div>

                    <div id="listaNotificaciones">
                        <p class="text-muted mb-0">
                            Sin notificaciones nuevas
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>
</header>


<main class="container dashboard-contenedor py-4">

    <?php if ($mensaje !== ""): ?>

        <div
            class="
                alert
                <?= $tipo === "error"
                    ? "alert-danger"
                    : "alert-success" ?>
            "
        >
            <?= esc($mensaje) ?>
        </div>

    <?php endif; ?>


    <!-- PERFIL -->

    <section class="dashboard-panel mb-4">

        <h2>
            Tu perfil: <?= esc($rol) ?>
        </h2>

        <p>
            <?= esc($descripciones[$rol] ?? "") ?>
        </p>

        <div class="row g-3">

            <div class="col-12 col-md-4">
                <small class="text-muted">Usuario</small>
                <div>
                    <strong><?= esc($usuarioSesion) ?></strong>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted">Correo</small>
                <div>
                    <strong><?= esc($emailSesion) ?></strong>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <small class="text-muted">Rol</small>
                <div>
                    <strong><?= esc($rol) ?></strong>
                </div>
            </div>

        </div>
    </section>


    <!-- ACCESOS -->

    <section class="mb-4">

        <h2 class="h5 mb-3">
            Accesos disponibles
        </h2>

        <div class="dashboard-accesos">

            <?php foreach ($accesos as $acceso): ?>

                <a
                    href="<?= esc($acceso["enlace"]) ?>"
                    class="acceso-dashboard"
                >
                    <i
                        class="bi <?= esc($acceso["icono"]) ?>"
                    ></i>

                    <h3>
                        <?= esc($acceso["titulo"]) ?>
                    </h3>

                    <p>
                        <?= esc($acceso["texto"]) ?>
                    </p>
                </a>

            <?php endforeach; ?>

        </div>

    </section>


    <?php if ($errorEstadisticas): ?>

        <div class="alert alert-warning">
            Algunas estadísticas no pudieron cargarse.
        </div>

    <?php endif; ?>


    <?php if (
        $puedeVerEstadisticas &&
        $totalAlumnos !== null
    ): ?>


        <!-- MÉTRICAS -->

        <section class="dashboard-metricas">

            <div class="metrica-dashboard">
                <i class="bi bi-mortarboard"></i>
                <strong><?= $totalAlumnos ?></strong>
                <span>Alumnos registrados</span>
            </div>

            <div class="metrica-dashboard">
                <i class="bi bi-calendar-check"></i>
                <strong><?= $altasRecientes ?></strong>
                <span>Altas últimos 7 días</span>
            </div>


            <?php if ($rol === "Administrador"): ?>

                <div class="metrica-dashboard">
                    <i class="bi bi-people"></i>
                    <strong><?= $totalUsuarios ?></strong>
                    <span>Usuarios</span>
                </div>

                <div class="metrica-dashboard">
                    <i class="bi bi-person-video3"></i>
                    <strong><?= $totalDocentes ?></strong>
                    <span>Docentes</span>
                </div>

            <?php endif; ?>

        </section>


        <!-- GRÁFICOS -->

        <section class="dashboard-grid">

            <div class="dashboard-panel">

                <h2>
                    Altas de alumnos — últimos 7 días
                </h2>

                <div class="dashboard-grafico">
                    <canvas id="graficoAltas"></canvas>
                </div>

            </div>


            <?php if ($rol === "Administrador"): ?>

                <div class="dashboard-panel">

                    <h2>
                        Usuarios por rol
                    </h2>

                    <div class="dashboard-grafico">
                        <canvas id="graficoRoles"></canvas>
                    </div>

                </div>

            <?php else: ?>

                <div class="dashboard-panel">
                    <h2>Panel docente</h2>

                    <p>
                        Podés consultar información académica
                        y el listado de alumnos.
                    </p>
                </div>

            <?php endif; ?>

        </section>


        <!-- ÚLTIMOS ALUMNOS -->

        <section class="dashboard-panel mb-4">

            <h2>
                Últimos alumnos registrados
            </h2>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Correo</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($ultimosAlumnos as $alumno): ?>

                        <tr>

                            <td>
                                <?= esc(
                                    $alumno["apellido"]
                                    . ", "
                                    . $alumno["nombre"]
                                ) ?>
                            </td>

                            <td>
                                <?= esc($alumno["email"]) ?>
                            </td>

                            <td>
                                <?= esc(
                                    date(
                                        "d/m/Y H:i",
                                        strtotime(
                                            $alumno["fecha_registro"]
                                        )
                                    )
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    <?php endif; ?>

</main>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<?php if (
    $puedeVerEstadisticas &&
    !$errorEstadisticas
): ?>

<script
    src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"
></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const altas = <?= json_encode(
        $datosAltas,
        JSON_UNESCAPED_UNICODE
    ) ?>;

    const roles = <?= json_encode(
        $datosRoles,
        JSON_UNESCAPED_UNICODE
    ) ?>;


    const canvasAltas =
        document.getElementById("graficoAltas");

    if (canvasAltas && typeof Chart !== "undefined") {

        new Chart(canvasAltas, {
            type: "line",

            data: {
                labels: altas.map(item => item.etiqueta),

                datasets: [{
                    data: altas.map(item => Number(item.cantidad)),
                    borderColor: "#4774bf",
                    backgroundColor: "rgba(71,116,191,.15)",
                    borderWidth: 3,
                    pointRadius: 5,
                    tension: .35,
                    fill: true
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }


    const canvasRoles =
        document.getElementById("graficoRoles");

    if (
        canvasRoles &&
        roles.length &&
        typeof Chart !== "undefined"
    ) {

        new Chart(canvasRoles, {
            type: "doughnut",

            data: {
                labels: roles.map(item => item.etiqueta),

                datasets: [{
                    data: roles.map(item => Number(item.cantidad)),

                    backgroundColor: [
                        "#253e66",
                        "#4774bf",
                        "#38a4b8",
                        "#e8a72f"
                    ],

                    borderColor: "#fff",
                    borderWidth: 3
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "62%",

                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });
    }

});
</script>

<?php endif; ?>


<script src="assets/js/notificaciones.js"></script>

</body>
</html>