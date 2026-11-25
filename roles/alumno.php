<?php
/*
 * roles/alumno.php
 * Módulo del Alumno: consulta de su propia información.
 * Consulta el legajo mediante usuario_id.
 */

$rolesPermitidos = ["Alumno"];
require_once __DIR__ . "/../includes/verificar_rol.php";


$usuarioId = (int) ($_SESSION["usuario_id"] ?? 0);

$consulta = $conexion->prepare(
    "SELECT id, nombre, apellido, email, fecha_registro
     FROM alumnos
     WHERE usuario_id = :usuario_id
     LIMIT 1"
);

$consulta->bindValue(
    ":usuario_id",
    $usuarioId,
    PDO::PARAM_INT
);

$consulta->execute();
$registroAlumno = $consulta->fetch();


$titulo = "Mi información académica";
$pagina = "panel";
require __DIR__ . "/../includes/header.php";
?>

    <main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1>Mi información académica</h1>
    </header>

        <section class="tarjeta-panel">

            <div class="mb-3">
                    <h2>Mis datos</h2>
                    <p>Información registrada en el instituto.</p>
            </div>

            <?php if ($registroAlumno): ?>

                <dl class="datos-cuenta mb-0">
                    <div>
                        <dt>Nombre</dt>
                        <dd><?= esc($registroAlumno["nombre"]) ?></dd>
                    </div>

                    <div>
                        <dt>Apellido</dt>
                        <dd><?= esc($registroAlumno["apellido"]) ?></dd>
                    </div>

                    <div>
                        <dt>Correo electrónico</dt>
                        <dd><?= esc($registroAlumno["email"]) ?></dd>
                    </div>

                    <div>
                        <dt>Alta en el instituto</dt>
                        <dd>
                            <?= esc(date("d/m/Y", strtotime($registroAlumno["fecha_registro"]))) ?>
                        </dd>
                    </div>
                </dl>

            <?php else: ?>

                <div class="estado-vacio">
                    <i class="bi bi-person-exclamation" aria-hidden="true"></i>
                    <h3>Tu cuenta todavía no está vinculada a un legajo</h3>
                    <p>
                        Pedile a la administración del instituto que revise el vínculo de tu cuenta.
                    </p>
                </div>

            <?php endif; ?>

        </section>

    </main>

    </body>
</html>
