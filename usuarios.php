<?php

require_once __DIR__ . "/includes/verificar_sesion.php";
require_once __DIR__ . "/config/conexion.php";


$titulo = "Usuarios";
$pagina = "usuarios";

$buscar = trim($_GET["buscar"] ?? "");
$rol = $_GET["rol"] ?? "";

$condiciones = [];
$parametros = [];

/* ==================================================
   BUSCADOR
================================================== */

if ($buscar !== "") {

    $condiciones[] =
        "(usuario LIKE :usuario
          OR email LIKE :email)";

    $parametros[":usuario"] =
        "%" . $buscar . "%";

    $parametros[":email"] =
        "%" . $buscar . "%";
}


/* ==================================================
   FILTRO DE ROL
================================================== */

$rolesPermitidos = [
    "Administrador",
    "Docente",
    "Alumno",
    "Invitado"
];

if (
    in_array(
        $rol,
        $rolesPermitidos,
        true
    )
) {

    $condiciones[] =
        "rol = :rol";

    $parametros[":rol"] =
        $rol;
}


/* ==================================================
   CONSULTA
================================================== */

$sql =
    "SELECT
        id,
        usuario,
        email,
        rol,
        fecha_registro
     FROM usuarios";

if ($condiciones) {

    $sql .=
        " WHERE "
        . implode(
            " AND ",
            $condiciones
        );
}

$sql .=
    " ORDER BY id ASC";

$consulta =
    $conexion->prepare($sql);

$consulta->execute($parametros);

$usuarios =
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
            <?= e($titulo) ?>
        </h1>

        <span>
            <i class="bi bi-building"></i>
            IES N.º 11
        </span>

    </div>


    <main class="p-3 p-md-4">

        <div class="tarjeta-panel">

            <h2>
                Gestión de usuarios

                <span
                    class="
                        text-muted
                        fw-normal
                    "
                >
                    (
                    <?= count($usuarios) ?>

                    resultado<?= count($usuarios) === 1
                        ? ""
                        : "s" ?>
                    )
                </span>
            </h2>


            <!-- ======================================
                 BUSCADOR Y FILTRO
            ======================================= -->

            <form
                method="GET"
                action="usuarios.php"
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
                        Buscar por usuario
                        o correo..."
                    value="<?= e($buscar) ?>"
                >


                <select
                    name="rol"
                    class="form-select"
                >

                    <option value="">
                        Todos los roles
                    </option>


                    <option
                        value="Administrador"
                        <?= $rol === "Administrador"
                            ? "selected"
                            : "" ?>
                    >
                        Administrador
                    </option>


                    <option
                        value="Docente"
                        <?= $rol === "Docente"
                            ? "selected"
                            : "" ?>
                    >
                        Docente
                    </option>


                    <option
                        value="Alumno"
                        <?= $rol === "Alumno"
                            ? "selected"
                            : "" ?>
                    >
                        Alumno
                    </option>


                    <option
                        value="Invitado"
                        <?= $rol === "Invitado"
                            ? "selected"
                            : "" ?>
                    >
                        Invitado
                    </option>

                </select>


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
                    href="usuarios.php"
                    class="
                        btn
                        btn-outline-secondary
                    "
                >
                    Limpiar
                </a>

            </form>


            <!-- ======================================
                 TABLA
            ======================================= -->

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

                            <th>Usuario</th>

                            <th>
                                Correo electrónico
                            </th>

                            <th>Rol</th>

                            <th>
                                Fecha de registro
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        count($usuarios) === 0
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
                                No se encontraron
                                usuarios con esos
                                criterios.
                            </td>

                        </tr>

                    <?php endif; ?>


                    <?php foreach (
                        $usuarios as $u
                    ): ?>


                        <?php

                        $claseRol = match (
                            $u["rol"]
                        ) {

                            "Administrador" =>
                                "text-bg-dark",

                            "Docente" =>
                                "text-bg-primary",

                            "Alumno" =>
                                "text-bg-info",

                            "Invitado" =>
                                "text-bg-warning",

                            default =>
                                "text-bg-secondary"
                        };

                        ?>


                        <tr>

                            <td>
                                <?= $u["id"] ?>
                            </td>


                            <td>
                                <?= e(
                                    $u["usuario"]
                                ) ?>
                            </td>


                            <td>
                                <?= e(
                                    $u["email"]
                                ) ?>
                            </td>


                            <td>

                                <span
                                    class="
                                        badge
                                        <?= $claseRol ?>
                                    "
                                >
                                    <?= e(
                                        $u["rol"]
                                    ) ?>
                                </span>

                            </td>


                            <td>

                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $u[
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