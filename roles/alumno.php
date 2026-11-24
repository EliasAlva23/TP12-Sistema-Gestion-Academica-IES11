<?php
/*
 * roles/alumno.php
 * Módulo del Alumno: consulta de su propia información.
 * Se vincula la cuenta con el registro de alumno por correo.
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


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel del alumno | Sistema de Gestión Académica</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>

<body>

    <?php require __DIR__ . "/../includes/navbar.php"; ?>

    <header class="encabezado">
        <div class="container py-4">
            <p class="etiqueta mb-2">Módulo del Alumno</p>
            <h1 class="mb-1">Mi información académica</h1>
            <p class="mb-0">
                Bienvenido/a, <?= esc($_SESSION["usuario"]) ?>.
            </p>
        </div>
    </header>

    <main class="container py-5">

        <section class="tarjeta-principal">

            <div class="cabecera-listado">
                <div>
                    <h2>Mis datos</h2>
                    <p>Información registrada en el instituto.</p>
                </div>
            </div>

            <?php if ($registroAlumno): ?>

                <div class="datos-cuenta">
                    <div>
                        <span>Nombre</span>
                        <strong><?= esc($registroAlumno["nombre"]) ?></strong>
                    </div>

                    <div>
                        <span>Apellido</span>
                        <strong><?= esc($registroAlumno["apellido"]) ?></strong>
                    </div>

                    <div>
                        <span>Correo electrónico</span>
                        <strong><?= esc($registroAlumno["email"]) ?></strong>
                    </div>

                    <div>
                        <span>Alta en el instituto</span>
                        <strong>
                            <?= esc(date("d/m/Y", strtotime($registroAlumno["fecha_registro"]))) ?>
                        </strong>
                    </div>
                </div>

            <?php else: ?>

                <div class="estado-vacio">
                    <i class="bi bi-person-exclamation"></i>
                    <h3>Tu cuenta todavía no está vinculada a un legajo</h3>
                    <p>
                        No encontramos un alumno registrado con el correo
                        <strong><?= esc($_SESSION["email"]) ?></strong>.
                        Pedile al administrador que te registre con ese
                        mismo correo.
                    </p>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
