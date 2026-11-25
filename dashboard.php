<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
/* ==================================================
   SESIÓN Y ROLES
================================================== */

$rol = $_SESSION["rol"] ?? "";
$usuarioSesion = $_SESSION["usuario"] ?? "";
$emailSesion = $_SESSION["email"] ?? "";

$mensaje = is_string($_GET["mensaje"] ?? null) ? $_GET["mensaje"] : "";
$tipo = $_GET["tipo"] ?? "";

$descripciones = [
    "Administrador" => "Tenés acceso completo a alumnos, usuarios, reportes, notificaciones y configuración.",
    "Docente" => "Podés consultar información académica y el listado de alumnos.",
    "Alumno" => "Podés consultar tu información académica personal.",
    "Invitado" => "Tenés acceso limitado a la información general del sistema."
];

/* ==================================================
   ACCESOS SEGÚN ROL
================================================== */

$accesos = [
    [
        "icono" => "bi-person-workspace",
        "titulo" => "Mi panel",
        "texto" => "Accedé a las funciones específicas de tu perfil.",
        "enlace" => PANELES_POR_ROL[$rol]
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

$puedeVerEstadisticas = puede_acceder("alumnos", $rol);

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

$puedeVerAvisos = puede_acceder("notificaciones", $rol);
$avisosRecientes = [];
if ($puedeVerAvisos) {
    require_once __DIR__ . "/includes/avisos.php";

    try {
        $avisosRecientes = avisos_visibles($conexion, (int) $_SESSION["usuario_id"], $rol, true);
    } catch (PDOException $error) {
        error_log("Avisos de Inicio: " . $error->getMessage());
    }
}

$mostrarEstadisticas = $puedeVerEstadisticas && $totalAlumnos !== null;
$titulo = "Inicio";
$pagina = "inicio";
require __DIR__ . "/includes/header.php";
?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
<header class="encabezado-pagina dashboard-cabecera">
    <div>

        <div class="d-flex justify-content-between align-items-center gap-3">

            <div>
                <h1 class="mt-1 mb-1">
                    Hola, <?= esc($usuarioSesion) ?>
                </h1>

                <p><span class="insignia-rol insignia-<?= esc(strtolower($rol)) ?>"><?= esc($rol) ?></span></p>
            </div>


            <?php if ($puedeVerAvisos): ?>
                <div class="dropdown" id="campanaAvisos"
                     data-avisos-url="<?= esc(url_app("notificaciones.php", ["formato" => "json"])) ?>">
                    <button type="button" class="btn btn-light position-relative" data-bs-toggle="dropdown"
                            aria-label="Ver notificaciones" aria-expanded="false">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                        <span id="contadorNotificaciones" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" aria-live="polite"
                              <?= $avisosRecientes ? "" : "hidden" ?>><?= count($avisosRecientes) > 5 ? "5+" : count($avisosRecientes) ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dashboard-avisos p-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                            <strong>Notificaciones</strong>
                            <a href="<?= esc(url_app("notificaciones.php")) ?>">Ver todas</a>
                        </div>
                        <div id="listaNotificaciones">
                            <?php if (!$avisosRecientes): ?>
                                <p class="text-muted mb-0">Sin notificaciones nuevas.</p>
                            <?php endif; ?>
                            <?php foreach (array_slice($avisosRecientes, 0, 5) as $aviso): ?>
                                <div class="notificacion-item">
                                    <strong><?= esc($aviso["titulo"]) ?></strong>
                                    <p><?= esc($aviso["mensaje"]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</header>




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

    <?php if ($errorEstadisticas): ?>

        <div class="alert alert-warning">
            Algunas estadísticas no pudieron cargarse.
        </div>

    <?php endif; ?>


    <?php if ($mostrarEstadisticas): ?>


        <!-- MÉTRICAS -->

        <section class="rejilla-metricas mb-4">

            <div class="tarjeta-metrica">
                <i class="bi bi-mortarboard" aria-hidden="true"></i>
                <strong class="valor"><?= $totalAlumnos ?></strong>
                <span class="etiqueta">Alumnos registrados</span>
            </div>

            <div class="tarjeta-metrica">
                <i class="bi bi-calendar-check" aria-hidden="true"></i>
                <strong class="valor"><?= $altasRecientes ?></strong>
                <span class="etiqueta">Altas últimos 7 días</span>
            </div>


            <?php if ($rol === "Administrador"): ?>

                <div class="tarjeta-metrica">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    <strong class="valor"><?= $totalUsuarios ?></strong>
                    <span class="etiqueta">Usuarios</span>
                </div>

                <div class="tarjeta-metrica">
                    <i class="bi bi-person-video3" aria-hidden="true"></i>
                    <strong class="valor"><?= $totalDocentes ?></strong>
                    <span class="etiqueta">Docentes</span>
                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

    <!-- ACCESOS -->

    <section class="mb-4">

        <h2 class="titulo-seccion mb-3">
            Accesos disponibles
        </h2>

        <div class="rejilla-accesos">

            <?php foreach ($accesos as $acceso): ?>

                <a
                    href="<?= esc(url_app($acceso["enlace"])) ?>"
                    class="tarjeta-acceso"
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


    <?php if ($mostrarEstadisticas): ?>
        <!-- GRÁFICOS -->

        <div class="rejilla-graficos mb-4">
            <?php
            $idGrafico = "graficoAltas";
            $tituloGrafico = "Altas de alumnos · últimos 7 días";
            $tipoGrafico = "line";
            $etiquetasGrafico = array_column($datosAltas, "etiqueta");
            $valoresGrafico = array_column($datosAltas, "cantidad");
            require __DIR__ . "/includes/grafico.php";
            ?>

            <?php if ($rol === "Administrador"): ?>
                <?php
            $idGrafico = "graficoRoles";
            $tituloGrafico = "Usuarios por rol";
            $tipoGrafico = "doughnut";
            $etiquetasGrafico = array_column($datosRoles, "etiqueta");
            $valoresGrafico = array_column($datosRoles, "cantidad");
            require __DIR__ . "/includes/grafico.php";
            ?>
            <?php endif; ?>
        </div>

        <!-- ÚLTIMOS ALUMNOS -->

        <section class="tarjeta-panel mb-4">

            <h2>
                Últimos alumnos registrados
            </h2>

            <div class="table-responsive" tabindex="0" role="region" aria-label="Últimos alumnos registrados">

                <table class="table table-hover tabla-datos tabla-registros">

                    <thead>
                        <tr>
                            <th scope="col">Alumno</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Fecha</th>
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


    <section class="tarjeta-panel">

        <h2>
            Tu perfil: <?= esc($rol) ?>
        </h2>

        <p>
            <?= esc($descripciones[$rol] ?? "") ?>
        </p>

        <dl class="datos-cuenta mb-0">
            <div><dt>Usuario</dt><dd><?= esc($usuarioSesion) ?></dd></div>
            <div><dt>Correo</dt><dd><?= esc($emailSesion) ?></dd></div>
        </dl>
    </section>


</main>


<?php if (
    $puedeVerEstadisticas &&
    !$errorEstadisticas
): ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>
<script src="<?= esc(url_app("assets/js/charts.js")) ?>" defer></script>

<?php endif; ?>


<?php if ($puedeVerAvisos): ?>
    <script src="<?= esc(url_app("assets/js/notificaciones.js")) ?>"></script>
<?php endif; ?>

</body>
</html>
