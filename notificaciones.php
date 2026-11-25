<?php

$formatoJson = ($_GET["formato"] ?? "") === "json";
$respuestaJson = $formatoJson;
$seccionRequerida = "notificaciones";
require_once __DIR__ . "/includes/verificar_rol.php";

$titulo = "Notificaciones";
$pagina = "notificaciones";


require_once __DIR__ . "/includes/avisos.php";

$notificaciones = avisos_visibles($conexion, (int) $_SESSION["usuario_id"], $_SESSION["rol"], $formatoJson);
if ($formatoJson) {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($notificaciones, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

require __DIR__ . "/includes/header.php";

?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1><?= esc($titulo) ?></h1>
        <p>Consultá los avisos disponibles para tu cuenta.</p>
    </header>

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

                        <?= esc(
                            $notificacion[
                                "titulo"
                            ]
                        ) ?>

                    </strong>


                    <p>

                        <?= esc(
                            $notificacion[
                                "mensaje"
                            ]
                        ) ?>

                    </p>


                    <time datetime="<?= esc(date(DATE_ATOM, strtotime($notificacion["fecha_creacion"]))) ?>">

                        <?= date(
                            "d/m/Y H:i",
                            strtotime(
                                $notificacion[
                                    "fecha_creacion"
                                ]
                            )
                        ) ?>

                    </time>

                </div>

            <?php endforeach; ?>

        </div>

    </main>

</body>

</html>
