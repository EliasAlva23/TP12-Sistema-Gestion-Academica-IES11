<?php

$seccionRequerida = "alumnos";
require_once __DIR__ . "/includes/verificar_rol.php";

$titulo = "Alumnos";

$pagina = "alumnos";
$puedeEditar = puede_acceder("crud_alumnos", $_SESSION["rol"]);
$mensajes = [
    "guardado" => ["success", "El alumno fue agregado."],
    "actualizado" => ["success", "Los datos del alumno fueron actualizados."],
    "eliminado" => ["success", "El alumno fue eliminado."],
    "no_encontrado" => ["warning", "El alumno indicado no está disponible."],
    "error_eliminar" => ["danger", "No fue posible eliminar el alumno."],
];
$codigoMensaje = $_GET["mensaje"] ?? "";
$aviso = is_string($codigoMensaje) ? ($mensajes[$codigoMensaje] ?? null) : null;


$buscar =
    trim(is_string($_GET["buscar"] ?? null) ? $_GET["buscar"] : "");


$parametros = [];


$sql =

    "SELECT
        id,
        nombre,
        apellido,
        email,
        fecha_registro
     FROM alumnos";


if ($buscar !== "") {

    $sql .=

        " WHERE
            nombre LIKE :nombre
            OR apellido LIKE :apellido
            OR email LIKE :email";

    $parametros = array_fill_keys([":nombre", ":apellido", ":email"], "%" . $buscar . "%");
}


$sql .=

    " ORDER BY
        fecha_registro DESC,
        id DESC";


$consulta =
    $conexion->prepare(
        $sql
    );


$consulta->execute(
    $parametros
);


$alumnos =
    $consulta->fetchAll();


require __DIR__ . "/includes/header.php";

?>


<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1><?= esc($titulo) ?></h1>
        <p>Buscá legajos por nombre, apellido o correo electrónico.</p>
    </header>
        <?php if ($aviso): ?>
            <p class="alert alert-<?= esc($aviso[0]) ?>" role="status"><?= esc($aviso[1]) ?></p>
        <?php endif; ?>


        <div class="tarjeta-panel">


            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                <h2 class="mb-0">Alumnos registrados</h2>
                <?php if ($puedeEditar): ?>
                    <a href="<?= esc(url_app("agregar.php")) ?>" class="btn boton-principal">Agregar alumno</a>
                <?php endif; ?>
            </div>


            <form
                method="GET"
                action="<?= esc(url_app("alumnos.php")) ?>"
                class="barra-filtros"
            >

                <div class="filtro-busqueda">
                    <label for="buscar-alumnos" class="form-label">Buscar alumnos</label>
                    <input id="buscar-alumnos" name="buscar" type="search" class="form-control"
                           placeholder="Nombre, apellido o correo" value="<?= esc($buscar) ?>">
                </div>


                <div class="acciones-filtro">
                    <button type="submit" class="btn boton-principal">
                        <i class="bi bi-search" aria-hidden="true"></i> Buscar
                    </button>
                    <a href="<?= esc(url_app("alumnos.php")) ?>" class="btn btn-outline-secondary">Limpiar</a>
                </div>

            </form>


            <div class="table-responsive" tabindex="0" role="region" aria-label="Alumnos registrados">

                <table
                    class="
                        table
                        table-hover
                        tabla-datos tabla-registros
                    "
                >

                    <thead>

                        <tr>

                            <th scope="col">ID</th>

                            <th scope="col">Nombre</th>

                            <th scope="col">Apellido</th>

                            <th scope="col">Correo</th>

                            <th scope="col">Fecha de alta</th>
                            <?php if ($puedeEditar): ?><th scope="col">Acciones</th><?php endif; ?>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        count($alumnos) === 0
                    ): ?>


                        <tr>

                            <td
                                colspan="<?= $puedeEditar ? 6 : 5 ?>"
                                class="
                                    text-center
                                    text-muted
                                    py-4
                                "
                            >

                                No se encontraron alumnos.

                            </td>

                        </tr>


                    <?php endif; ?>


                    <?php foreach (
                        $alumnos
                        as $alumno
                    ): ?>


                        <tr>

                            <td>

                                <?= $alumno["id"] ?>

                            </td>


                            <td>

                                <?= esc(
                                    $alumno["nombre"]
                                ) ?>

                            </td>


                            <td>

                                <?= esc(
                                    $alumno["apellido"]
                                ) ?>

                            </td>


                            <td>

                                <?= esc(
                                    $alumno["email"]
                                ) ?>

                            </td>


                            <td>

                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $alumno[
                                            "fecha_registro"
                                        ]
                                    )
                                ) ?>

                            </td>
                            <?php if ($puedeEditar): ?>
                                <td>
                                    <div class="acciones-tabla">
                                        <a class="btn btn-sm btn-outline-primary"
                                           aria-label="Editar a <?= esc($alumno["nombre"] . " " . $alumno["apellido"]) ?>"
                                           href="<?= esc(url_app("editar.php", ["id" => (int) $alumno["id"]])) ?>">Editar</a>
                                        <a class="btn btn-sm btn-outline-danger"
                                           aria-label="Eliminar a <?= esc($alumno["nombre"] . " " . $alumno["apellido"]) ?>"
                                           href="<?= esc(url_app("eliminar.php", ["id" => (int) $alumno["id"]])) ?>">Eliminar</a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>

</html>
