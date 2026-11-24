<?php
require_once __DIR__ . "/includes/verificar_sesion.php";

require_once "config/conexion.php";

$titulo = "Notificaciones";
$pagina = "notificaciones";


$consulta =
    $conexion->query(
        "SELECT
            id,
            tipo,
            titulo,
            mensaje,
            rol_destino,
            leida,
            fecha_creacion
         FROM notificaciones
         ORDER BY
            fecha_creacion DESC,
            id DESC"
    );


$notificaciones =
    $consulta->fetchAll();


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

            Notificaciones

        </h1>


        <span>

            <i class="bi bi-bell"></i>

            IES N.º 11

        </span>

    </div>


    <main class="p-3 p-md-4">

        <div class="tarjeta-panel">

            <h2>
                Historial de notificaciones
            </h2>


            <?php if (
                count(
                    $notificaciones
                ) === 0
            ): ?>

                <div
                    class="
                        alert
                        alert-info
                    "
                >

                    Todavía no existen
                    notificaciones registradas.

                </div>

            <?php endif; ?>


            <?php foreach (
                $notificaciones
                as $notificacion
            ): ?>

                <div
                    class="
                        notificacion-item
                        mb-2
                    "
                >

                    <strong>

                        <?= e(
                            $notificacion[
                                "titulo"
                            ]
                        ) ?>

                    </strong>


                    <p>

                        <?= e(
                            $notificacion[
                                "mensaje"
                            ]
                        ) ?>

                    </p>


                    <small>

                        <?= date(
                            "d/m/Y H:i",
                            strtotime(
                                $notificacion[
                                    "fecha_creacion"
                                ]
                            )
                        ) ?>

                    </small>

                </div>

            <?php endforeach; ?>

        </div>

    </main>

</div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script
    src="assets/js/scripts.js"
></script>

<script
    src="assets/js/notificaciones.js"
></script>

</body>

</html>
