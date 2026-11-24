<?php
/*
 * roles/docente.php
 * Módulo del Docente: consulta de alumnos (solo lectura).
 */

$rolesPermitidos = ["Docente"];
require_once __DIR__ . "/../includes/verificar_rol.php";

$totalAlumnos = (int) $conexion
    ->query("SELECT COUNT(*) FROM alumnos")
    ->fetchColumn();

$ultimos = $conexion->query(
    "SELECT nombre, apellido, email, fecha_registro
     FROM alumnos
     ORDER BY fecha_registro DESC, id DESC
     LIMIT 5"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel docente | Sistema de Gestión Académica</title>

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
            <p class="etiqueta mb-2">Módulo del Docente</p>
            <h1 class="mb-1">Panel docente</h1>
            <p class="mb-0">
                Bienvenido/a, <?= esc($_SESSION["usuario"]) ?>.
            </p>
        </div>
    </header>

    <main class="container py-5">

        <section class="fila-metricas mb-4">
            <div class="metrica">
                <i class="bi bi-mortarboard"></i>
                <div>
                    <strong><?= $totalAlumnos ?></strong>
                    <span>Alumnos registrados</span>
                </div>
            </div>
        </section>

        <section class="tarjeta-principal">

            <div class="cabecera-listado">
                <div>
                    <h2>Últimos alumnos registrados</h2>
                    <p>
                        Tenés acceso de consulta. Para modificar
                        registros, contactá a un administrador.
                    </p>
                </div>

                <a href="../index.php" class="btn boton-principal">
                    <i class="bi bi-list-ul"></i>
                    Ver listado completo
                </a>
            </div>

            <?php if (count($ultimos) > 0): ?>
                <div class="table-responsive">
                    <table class="table tabla-alumnos align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo electrónico</th>
                                <th>Fecha de registro</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ultimos as $a): ?>
                            <tr>
                                <td><?= esc($a["nombre"]) ?></td>
                                <td><?= esc($a["apellido"]) ?></td>
                                <td><?= esc($a["email"]) ?></td>
                                <td>
                                    <?= esc(date("d/m/Y H:i", strtotime($a["fecha_registro"]))) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="estado-vacio">
                    <i class="bi bi-people"></i>
                    <h3>Todavía no hay alumnos registrados</h3>
                </div>
            <?php endif; ?>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
