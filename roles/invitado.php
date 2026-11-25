<?php
/*
 * roles/invitado.php
 * Módulo del Invitado: acceso limitado, solo información general.
 */

$rolesPermitidos = ["Invitado"];
require_once __DIR__ . "/../includes/verificar_rol.php";
$titulo = "Panel de invitado";
$pagina = "panel";
require __DIR__ . "/../includes/header.php";
?>

    <main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1>Panel de invitado</h1>
    </header>

        <section class="tarjeta-panel">

            <div class="mb-3">
                    <h2>Información general</h2>
                    <p>Datos públicos del instituto.</p>
            </div>

            <dl class="datos-cuenta mb-0">
                <div>
                    <dt>Institución</dt>
                    <dd>Instituto de Educación Superior N.º 11</dd>
                </div>

                <div>
                    <dt>Carrera</dt>
                    <dd>Tecnicatura Superior en Desarrollo de Software</dd>
                </div>

                <div>
                    <dt>Tu nivel de acceso</dt>
                    <dd>Solo lectura</dd>
                </div>
            </dl>

            <div class="alert alert-info mt-4 mb-0 d-flex gap-2">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <p>
                    Con el perfil de invitado no podés ver datos de
                    alumnos ni modificar información. Si necesitás más
                    permisos, solicitá a un administrador que cambie
                    el rol de tu cuenta.
                </p>
            </div>

        </section>

    </main>

    </body>
</html>
