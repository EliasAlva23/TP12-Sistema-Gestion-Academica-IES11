<?php

require_once __DIR__ . "/config/conexion.php";

header("Content-Type: text/html; charset=UTF-8");

/* Clave temporal definida en Railway */
$claveEsperada = getenv("SETUP_KEY") ?: "";
$claveRecibida = $_GET["key"] ?? "";

if (
    $claveEsperada === "" ||
    !hash_equals($claveEsperada, $claveRecibida)
) {
    http_response_code(403);
    exit("Acceso no autorizado.");
}

try {

    /* =========================
       USUARIOS
    ========================== */
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            usuario VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM(
                'Administrador',
                'Docente',
                'Alumno',
                'Invitado'
            ) NOT NULL DEFAULT 'Alumno',
            fecha_registro TIMESTAMP
                NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_usuario (usuario),
            UNIQUE KEY uq_email_usuario (email)
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci"
    );

    /* =========================
       ALUMNOS
    ========================== */
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS alumnos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            usuario_id INT UNSIGNED NULL,
            nombre VARCHAR(60) NOT NULL,
            apellido VARCHAR(60) NOT NULL,
            email VARCHAR(100) NOT NULL,
            fecha_registro TIMESTAMP
                NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_email_alumno (email),
            KEY idx_usuario_id (usuario_id)
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci"
    );

    /* =========================
       INTENTOS LOGIN
    ========================== */
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS intentos_login (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            clave_hash VARCHAR(64) NOT NULL,
            fecha_intento TIMESTAMP
                NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_clave_hash (clave_hash),
            KEY idx_fecha_intento (fecha_intento)
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci"
    );

    /* =========================
       NOTIFICACIONES
    ========================== */
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS notificaciones (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            tipo VARCHAR(50) NOT NULL,
            titulo VARCHAR(150) NOT NULL,
            mensaje VARCHAR(255) NOT NULL,
            usuario_id INT UNSIGNED NULL,
            rol_destino VARCHAR(30) NULL,
            leida TINYINT(1) NOT NULL DEFAULT 0,
            fecha_creacion TIMESTAMP
                NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_notificacion_usuario (usuario_id),
            KEY idx_rol_destino (rol_destino),
            KEY idx_fecha_creacion (fecha_creacion)
        ) ENGINE=InnoDB
          DEFAULT CHARSET=utf8mb4
          COLLATE=utf8mb4_unicode_ci"
    );


    /* =========================
       USUARIOS DE PRUEBA
    ========================== */

    $usuarios = [
        [
            "admin",
            "admin@ies11.edu.ar",
            "Admin2026",
            "Administrador"
        ],
        [
            "docente",
            "docente@ies11.edu.ar",
            "Docente2026",
            "Docente"
        ],
        [
            "alumno",
            "alumno@ies11.edu.ar",
            "Alumno2026",
            "Alumno"
        ],
        [
            "invitado",
            "invitado@ies11.edu.ar",
            "Invitado2026",
            "Invitado"
        ]
    ];

    $insertarUsuario = $conexion->prepare(
        "INSERT INTO usuarios (
            usuario,
            email,
            password,
            rol
        )
        VALUES (
            :usuario,
            :email,
            :password,
            :rol
        )
        ON DUPLICATE KEY UPDATE
            email = VALUES(email),
            password = VALUES(password),
            rol = VALUES(rol)"
    );

    foreach ($usuarios as $u) {
        $insertarUsuario->execute([
            ":usuario" => $u[0],
            ":email" => $u[1],
            ":password" => password_hash(
                $u[2],
                PASSWORD_DEFAULT
            ),
            ":rol" => $u[3]
        ]);
    }


    /* =========================
       ALUMNO VINCULADO
    ========================== */

    $consultaAlumno = $conexion->prepare(
        "SELECT id
         FROM usuarios
         WHERE usuario = 'alumno'
         LIMIT 1"
    );

    $consultaAlumno->execute();

    $usuarioAlumnoId =
        (int) $consultaAlumno->fetchColumn();


    $insertarAlumno = $conexion->prepare(
        "INSERT INTO alumnos (
            usuario_id,
            nombre,
            apellido,
            email
        )
        VALUES (
            :usuario_id,
            :nombre,
            :apellido,
            :email
        )
        ON DUPLICATE KEY UPDATE
            usuario_id = VALUES(usuario_id),
            nombre = VALUES(nombre),
            apellido = VALUES(apellido)"
    );

    $insertarAlumno->execute([
        ":usuario_id" => $usuarioAlumnoId,
        ":nombre" => "Alumno",
        ":apellido" => "Prueba",
        ":email" => "alumno@ies11.edu.ar"
    ]);


    /* Datos adicionales para el dashboard */
    $conexion->exec(
        "INSERT IGNORE INTO alumnos (
            nombre,
            apellido,
            email
        )
        VALUES
        ('Ana', 'Martinez',
         'ana.martinez@ies11.edu.ar'),

        ('Lucas', 'Rivero',
         'lucas.rivero@ies11.edu.ar'),

        ('Marta', 'Salva',
         'marta.salva@ies11.edu.ar')"
    );


    /* =========================
       NOTIFICACIÓN INICIAL
    ========================== */

    $cantidadNotificaciones =
        (int) $conexion
            ->query(
                "SELECT COUNT(*)
                 FROM notificaciones"
            )
            ->fetchColumn();

    if ($cantidadNotificaciones === 0) {

        $conexion->exec(
            "INSERT INTO notificaciones (
                tipo,
                titulo,
                mensaje,
                rol_destino
            )
            VALUES (
                'sistema',
                'Sistema desplegado',
                'El Sistema de Gestión Académica fue publicado correctamente.',
                'Administrador'
            )"
        );
    }


    echo "
        <h1>Instalación completada ✅</h1>

        <p>La base de datos fue creada correctamente.</p>

        <ul>
            <li>usuarios ✅</li>
            <li>alumnos ✅</li>
            <li>intentos_login ✅</li>
            <li>notificaciones ✅</li>
        </ul>

        <p>
            Ahora eliminá setup_database.php
            del proyecto.
        </p>
    ";

} catch (PDOException $error) {

    http_response_code(500);

    echo "<h1>Error</h1>";

    echo "<pre>"
        . htmlspecialchars(
            $error->getMessage(),
            ENT_QUOTES,
            "UTF-8"
        )
        . "</pre>";
}
