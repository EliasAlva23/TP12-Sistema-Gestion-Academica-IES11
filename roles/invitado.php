<?php
/*
 * roles/invitado.php
 * Módulo del Invitado: acceso limitado, solo información general.
 */

$rolesPermitidos = ["Invitado"];
require_once __DIR__ . "/../includes/verificar_rol.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel de invitado | Sistema de Gestión Académica</title>

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
            <p class="etiqueta mb-2">Módulo del Invitado</p>
            <h1 class="mb-1">Panel de invitado</h1>
            <p class="mb-0">
                Bienvenido/a, <?= esc($_SESSION["usuario"]) ?>.
            </p>
        </div>
    </header>

    <main class="container py-5">

        <section class="tarjeta-principal">

            <div class="cabecera-listado">
                <div>
                    <h2>Información general</h2>
                    <p>Datos públicos del instituto.</p>
                </div>
            </div>

            <div class="datos-cuenta">
                <div>
                    <span>Institución</span>
                    <strong>Instituto de Educación Superior N.º 11</strong>
                </div>

                <div>
                    <span>Carrera</span>
                    <strong>Tecnicatura Superior en Desarrollo de Software</strong>
                </div>

                <div>
                    <span>Tu nivel de acceso</span>
                    <strong>Solo lectura</strong>
                </div>
            </div>

            <div class="aviso-permisos">
                <i class="bi bi-info-circle"></i>
                <p>
                    Con el perfil de invitado no podés ver datos de
                    alumnos ni modificar información. Si necesitás más
                    permisos, solicitá a un administrador que cambie
                    el rol de tu cuenta.
                </p>
            </div>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
