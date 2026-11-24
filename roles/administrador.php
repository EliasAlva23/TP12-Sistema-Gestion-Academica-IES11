<?php
/*
 * roles/administrador.php
 * Módulo del Administrador: gestión de usuarios y roles.
 */

$rolesPermitidos = ["Administrador"];
require_once __DIR__ . "/../includes/verificar_rol.php";

$rolesValidos = ["Administrador", "Docente", "Alumno", "Invitado"];
$miId = (int) $_SESSION["usuario_id"];

/* Vuelve a esta página con un mensaje */
function volver_con_mensaje(string $mensaje, string $tipo): void
{
    header(
        "Location: administrador.php?" . http_build_query([
            "tipo" => $tipo,
            "mensaje" => $mensaje
        ])
    );
    exit;
}

/* ------------------------------------------------------------
 * Procesa las acciones enviadas por formulario (POST)
 * ------------------------------------------------------------ */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!csrf_validar($_POST["csrf"] ?? null)) {
        volver_con_mensaje(
            "La solicitud no es válida. Intentá nuevamente.",
            "error"
        );
    }

    $accion = $_POST["accion"] ?? "";
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        volver_con_mensaje("El usuario indicado no es válido.", "error");
    }

    /* El administrador no puede modificarse a sí mismo */
    if ($id === $miId) {
        volver_con_mensaje(
            "No podés modificar ni eliminar tu propia cuenta.",
            "error"
        );
    }

    try {

        if ($accion === "cambiar_rol") {

            $nuevoRol = $_POST["rol"] ?? "";

            if (!in_array($nuevoRol, $rolesValidos, true)) {
                volver_con_mensaje("El rol seleccionado no es válido.", "error");
            }

            $consulta = $conexion->prepare(
                "UPDATE usuarios SET rol = :rol WHERE id = :id"
            );
            $consulta->execute([":rol" => $nuevoRol, ":id" => $id]);

            volver_con_mensaje("El rol fue actualizado correctamente.", "exito");
        }

        if ($accion === "eliminar") {

            $consulta = $conexion->prepare(
                "DELETE FROM usuarios WHERE id = :id"
            );
            $consulta->execute([":id" => $id]);

            volver_con_mensaje("El usuario fue eliminado correctamente.", "exito");
        }

        volver_con_mensaje("La acción solicitada no es válida.", "error");

    } catch (PDOException $error) {
        volver_con_mensaje(
            "No fue posible completar la operación.",
            "error"
        );
    }
}

/* ------------------------------------------------------------
 * Datos para mostrar
 * ------------------------------------------------------------ */
$mensaje = $_GET["mensaje"] ?? "";
$tipo = $_GET["tipo"] ?? "";

$usuarios = $conexion->query(
    "SELECT id, usuario, email, rol, fecha_registro
     FROM usuarios
     ORDER BY id ASC"
)->fetchAll();

/* Cantidad de usuarios por rol */
$porRol = array_fill_keys($rolesValidos, 0);

foreach ($usuarios as $u) {
    if (isset($porRol[$u["rol"]])) {
        $porRol[$u["rol"]]++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Administración | Sistema de Gestión Académica</title>

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
            <p class="etiqueta mb-2">Módulo del Administrador</p>
            <h1 class="mb-1">Gestión de usuarios y roles</h1>
            <p class="mb-0">
                Controlá quién accede al sistema y con qué permisos.
            </p>
        </div>
    </header>

    <main class="container py-5">

        <?php if ($mensaje !== ""): ?>
            <div
                class="alert <?= $tipo === "error"
                    ? "alert-danger"
                    : "alert-success" ?>"
                role="alert"
            >
                <?= esc($mensaje) ?>
            </div>
        <?php endif; ?>

        <section class="fila-metricas mb-4">
            <?php foreach ($porRol as $nombreRol => $cantidad): ?>
                <div class="metrica">
                    <span class="insignia-rol insignia-<?= esc(strtolower($nombreRol)) ?>">
                        <?= esc($nombreRol) ?>
                    </span>
                    <div>
                        <strong><?= $cantidad ?></strong>
                        <span>cuenta<?= $cantidad === 1 ? "" : "s" ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="tarjeta-principal">

            <div class="cabecera-listado">
                <div>
                    <h2>Usuarios registrados</h2>
                    <p>
                        Cambiá el rol de una cuenta o eliminala.
                        Tu propia cuenta está protegida.
                    </p>
                </div>

                <a href="../index.php" class="btn boton-principal">
                    <i class="bi bi-people"></i>
                    Gestionar alumnos
                </a>
            </div>

            <div class="table-responsive">
                <table class="table tabla-alumnos align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Correo electrónico</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <?php $esMiCuenta = ((int) $u["id"] === $miId); ?>
                        <tr>
                            <td><?= esc($u["id"]) ?></td>

                            <td>
                                <?= esc($u["usuario"]) ?>
                                <?php if ($esMiCuenta): ?>
                                    <span class="badge text-bg-secondary">Vos</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($u["email"]) ?></td>

                            <td>
                                <?php if ($esMiCuenta): ?>
                                    <span class="insignia-rol insignia-<?= esc(strtolower($u["rol"])) ?>">
                                        <?= esc($u["rol"]) ?>
                                    </span>
                                <?php else: ?>
                                    <form
                                        method="POST"
                                        class="form-cambiar-rol"
                                    >
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="accion" value="cambiar_rol">
                                        <input type="hidden" name="id" value="<?= (int) $u["id"] ?>">

                                        <select
                                            name="rol"
                                            class="form-select form-select-sm"
                                            aria-label="Rol de <?= esc($u["usuario"]) ?>"
                                        >
                                            <?php foreach ($rolesValidos as $r): ?>
                                                <option
                                                    value="<?= esc($r) ?>"
                                                    <?= $u["rol"] === $r ? "selected" : "" ?>
                                                >
                                                    <?= esc($r) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button
                                            type="submit"
                                            class="btn btn-sm boton-secundario"
                                        >
                                            Guardar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= esc(date("d/m/Y", strtotime($u["fecha_registro"]))) ?>
                            </td>

                            <td class="text-center">
                                <?php if (!$esMiCuenta): ?>
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar a este usuario? Esta acción no se puede deshacer.');"
                                    >
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $u["id"] ?>">

                                        <button
                                            type="submit"
                                            class="btn boton-eliminar"
                                        >
                                            <i class="bi bi-trash3"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </section>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
