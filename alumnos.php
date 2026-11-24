<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
require_once "config/conexion.php";

$titulo = "Alumnos";

$pagina = "alumnos";


$buscar =
    trim(
        $_GET["buscar"]
        ?? ""
    );


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
            nombre LIKE :buscar
            OR apellido LIKE :buscar
            OR email LIKE :buscar";

    $parametros[":buscar"] =
        "%" . $buscar . "%";
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

            Alumnos

        </h1>


        <span>

            <i class="bi bi-mortarboard"></i>

            IES N.º 11

        </span>

    </div>


    <main class="p-3 p-md-4">


        <div class="tarjeta-panel">


            <h2>
                Alumnos registrados
            </h2>


            <form
                method="GET"
                class="barra-filtros"
            >

                <input
                    type="search"
                    name="buscar"
                    class="
                        form-control
                        buscador
                    "
                    placeholder="
                        Buscar por nombre,
                        apellido o correo..."
                    value="<?= e($buscar) ?>"
                >


                <button
                    type="submit"
                    class="
                        btn
                        boton-principal
                    "
                >

                    <i class="bi bi-search"></i>

                    Buscar

                </button>


                <a
                    href="alumnos.php"
                    class="
                        btn
                        btn-outline-secondary
                    "
                >

                    Limpiar

                </a>

            </form>


            <div class="table-responsive">

                <table
                    class="
                        table
                        table-hover
                        tabla-datos
                    "
                >

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Nombre</th>

                            <th>Apellido</th>

                            <th>Correo</th>

                            <th>Fecha de alta</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        count($alumnos) === 0
                    ): ?>


                        <tr>

                            <td
                                colspan="5"
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

                                <?= e(
                                    $alumno["nombre"]
                                ) ?>

                            </td>


                            <td>

                                <?= e(
                                    $alumno["apellido"]
                                ) ?>

                            </td>


                            <td>

                                <?= e(
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

                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


</div>


<script
    src="
    https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script
    src="assets/js/scripts.js"
></script>


</body>

</html>