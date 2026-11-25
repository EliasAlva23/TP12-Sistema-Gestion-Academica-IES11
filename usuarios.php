<?php

$seccionRequerida = "usuarios";
require_once __DIR__ . "/includes/verificar_rol.php";


$titulo = "Usuarios";
$pagina = "usuarios";
$rolesValidos = array_keys(PANELES_POR_ROL);
$miId = (int) $_SESSION["usuario_id"];
$mensajes = [
    "rol_actualizado" => ["success", "El rol fue guardado."],
    "eliminado" => ["success", "La cuenta fue eliminada."],
    "no_encontrado" => ["warning", "La cuenta indicada no está disponible."],
    "cuenta_propia" => ["warning", "No podés modificar ni eliminar tu propia cuenta."],
    "error" => ["danger", "No fue posible completar la operación."],
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_validar($_POST["csrf"] ?? null)) {
        error_acceso(403, "La solicitud de administración de usuarios no es válida.");
    }

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $accion = $_POST["accion"] ?? "";
    $nuevoRol = $_POST["rol"] ?? "";

    if (!$id) {
        redirigir("usuarios.php", ["mensaje" => "no_encontrado"], 303);
    }

    if ($id === $miId) {
        redirigir("usuarios.php", ["mensaje" => "cuenta_propia"], 303);
    }

    try {
        if ($accion === "cambiar_rol" && in_array($nuevoRol, $rolesValidos, true)) {
            $consulta = $conexion->prepare("UPDATE usuarios SET rol = :rol WHERE id = :id");
            $consulta->execute([":rol" => $nuevoRol, ":id" => $id]);
            $resultado = "rol_actualizado";
            if ($consulta->rowCount() === 0) {
                $consulta = $conexion->prepare("SELECT id FROM usuarios WHERE id = :id");
                $consulta->execute([":id" => $id]);
                $resultado = $consulta->fetchColumn() ? "rol_actualizado" : "no_encontrado";
            }
        } elseif ($accion === "eliminar") {
            $conexion->beginTransaction();

            $consulta = $conexion->prepare("SELECT id FROM usuarios WHERE id = :id FOR UPDATE");
            $consulta->execute([":id" => $id]);
            $resultado = "no_encontrado";
            if ($consulta->fetchColumn()) {
                $consulta = $conexion->prepare("UPDATE alumnos SET usuario_id = NULL WHERE usuario_id = :id");
                $consulta->execute([":id" => $id]);
                $consulta = $conexion->prepare("DELETE FROM usuarios WHERE id = :id");
                $consulta->execute([":id" => $id]);
                $resultado = "eliminado";
            }

            $conexion->commit();
        } else {
            $resultado = "error";
        }
    } catch (PDOException $error) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        error_log("Administración de usuarios: " . $error->getMessage());
        $resultado = "error";
    }

    redirigir("usuarios.php", ["mensaje" => $resultado], 303);
}

$codigoMensaje = $_GET["mensaje"] ?? "";
$aviso = is_string($codigoMensaje) ? ($mensajes[$codigoMensaje] ?? null) : null;

$buscar = trim(is_string($_GET["buscar"] ?? null) ? $_GET["buscar"] : "");
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


if (
    in_array(
        $rol,
        $rolesValidos,
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


require __DIR__ . "/includes/header.php";

?>

<main id="contenido-principal" tabindex="-1" class="pagina-contenido">
    <header class="encabezado-pagina">
        <h1><?= esc($titulo) ?></h1>
        <p>Consultá las cuentas del sistema y administrá sus roles.</p>
    </header>
        <?php if ($aviso): ?>
            <p class="alert alert-<?= esc($aviso[0]) ?>" role="status"><?= esc($aviso[1]) ?></p>
        <?php endif; ?>

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
                action="<?= esc(url_app("usuarios.php")) ?>"
                class="barra-filtros"
            >

                <div class="filtro-busqueda">
                    <label for="buscar-usuarios" class="form-label">Buscar usuarios</label>
                    <input id="buscar-usuarios" name="buscar" type="search" class="form-control"
                           placeholder="Usuario o correo" value="<?= esc($buscar) ?>">
                </div>


                <div class="filtro-rol">
                    <label for="filtrar-rol" class="form-label">Rol</label>
                    <select id="filtrar-rol" name="rol" class="form-select">
                        <option value="">Todos los roles</option>
                        <?php foreach ($rolesValidos as $rolValido): ?>
                            <option value="<?= esc($rolValido) ?>"<?= $rol === $rolValido ? " selected" : "" ?>>
                                <?= esc($rolValido) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="acciones-filtro">
                    <button type="submit" class="btn boton-principal">
                        <i class="bi bi-search" aria-hidden="true"></i> Buscar
                    </button>
                    <a href="<?= esc(url_app("usuarios.php")) ?>" class="btn btn-outline-secondary">Limpiar</a>
                </div>

            </form>


            <!-- ======================================
                 TABLA
            ======================================= -->

            <div class="table-responsive" tabindex="0" role="region" aria-label="Cuentas registradas">

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

                            <th scope="col">Usuario</th>

                            <th scope="col">
                                Correo electrónico
                            </th>
                            <th scope="col">Rol</th>

                            <th scope="col">
                                Fecha de registro
                            </th>
                            <th scope="col">Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        count($usuarios) === 0
                    ): ?>

                        <tr>

                            <td
                                colspan="6"
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


                                                <tr>

                            <td>
                                <?= $u["id"] ?>
                            </td>


                            <td>
                                <?= esc(
                                    $u["usuario"]
                                ) ?>
                            </td>


                            <td>
                                <?= esc(
                                    $u["email"]
                                ) ?>
                            </td>


                            <td>

                                <span class="insignia-rol insignia-<?= esc(strtolower($u["rol"])) ?>">
                                    <?= esc(
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
                            <td>
                                <?php if ((int) $u["id"] !== $miId): ?>
                                    <div class="acciones-tabla">
                                        <form method="post" action="<?= esc(url_app("usuarios.php")) ?>">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="accion" value="cambiar_rol">
                                            <input type="hidden" name="id" value="<?= (int) $u["id"] ?>">
                                            <select name="rol" class="form-select form-select-sm" aria-label="Rol de <?= esc($u["usuario"]) ?>">
                                                <?php foreach ($rolesValidos as $rolValido): ?>
                                                    <option value="<?= esc($rolValido) ?>"<?= $u["rol"] === $rolValido ? " selected" : "" ?>>
                                                        <?= esc($rolValido) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary" aria-label="Guardar rol de <?= esc($u["usuario"]) ?>">Guardar rol</button>
                                        </form>
                                        <form method="post" action="<?= esc(url_app("usuarios.php")) ?>"
                                              onsubmit="return confirm('¿Eliminar esta cuenta?');">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= (int) $u["id"] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Eliminar a <?= esc($u["usuario"]) ?>">Eliminar</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Tu cuenta</span>
                                <?php endif; ?>
                            </td>
                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>

</html>
