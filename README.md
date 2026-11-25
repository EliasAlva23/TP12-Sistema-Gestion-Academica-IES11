# Sistema de Gestión Académica - IES N.º 11

Proyecto Final Integrador desarrollado para la materia Diseño de Interfaz
de la Tecnicatura Superior en Desarrollo de Software del IES N.º 11.

## Descripción

Sistema web para la gestión académica que integra autenticación de usuarios,
roles, gestión de alumnos, dashboard administrativo, reportes y
notificaciones en tiempo real.

## Tecnologías utilizadas

- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- PHP
- MySQL / MariaDB
- PDO
- Chart.js
- WebSockets
- Git y GitHub

## Funcionalidades

- Inicio y cierre de sesión.
- Registro de usuarios.
- Roles:
  - Administrador
  - Docente
  - Alumno
  - Invitado
- Gestión CRUD de alumnos.
- Gestión y filtrado de usuarios.
- Dashboard con métricas dinámicas.
- Gráficos estadísticos.
- Reportes académicos.
- Notificaciones en tiempo real mediante WebSockets.
- Diseño responsive.

## Base de datos

Base de datos:

`instituto`

Tablas principales:

- alumnos
- usuarios
- intentos_login
- notificaciones

El archivo de instalación se encuentra en:

`database/instituto.sql`

## Instalación local

1. Copiá el proyecto dentro de `C:\xampp\htdocs\` e iniciá Apache y MySQL en XAMPP.
2. Creá la base `instituto` en phpMyAdmin e importá `database/instituto.sql` en ella.
3. Verificá los datos de conexión local en `config/conexion.php`. El puerto previsto es 3307. También podés definir `DATABASE_URL` en el entorno de PHP.
4. Abrí `http://localhost/TP12_IES11_Final/`. Apache debe aplicar `database/.htaccess` para impedir que se descargue el volcado. Usá el volcado de demostración solamente en un entorno de prueba.

## Navegación y permisos

| Ruta | Acceso |
| --- | --- |
| `index.php` | Entrada. Lleva al inicio de sesión o a Inicio según la sesión. |
| `login.php`, `registro.php` | Acceso y registro. El alta pública ofrece Alumno e Invitado. |
| `recuperar_contrasena.php` | Información de contacto para recuperar una cuenta. |
| `dashboard.php` | Inicio autenticado para los cuatro roles. |
| `roles/administrador.php`, `roles/docente.php`, `roles/alumno.php`, `roles/invitado.php` | Mi panel de cada rol. |
| `alumnos.php` | Consulta para Administrador y Docente. Altas, cambios y bajas para Administrador. |
| `usuarios.php`, `reportes.php`, `configuracion.php` | Administrador. |
| `notificaciones.php` | Avisos autorizados para Administrador, Docente y Alumno. |
| `cambiar_contrasena.php`, `logout.php` | Contraseña propia y salida para los cuatro roles. El cierre de sesión usa el botón Salir. |

## Railway

El Dockerfile inicia el servicio HTTP en `PORT` con `pdo_mysql`. Configurá `DATABASE_URL` con la conexión MySQL provista al servicio web. La imagen HTTP excluye el volcado de la base y el servidor local de WebSocket. Los avisos guardados siguen disponibles en Inicio y Notificaciones.

## WebSocket local

En otra consola, con MySQL iniciado:

```bat
cd C:\xampp\htdocs\TP12_IES11_Final\websocket
C:\xampp\php\php.exe servidor.php
```

Accedé a la app por HTTP desde `localhost` o `127.0.0.1`. El servidor escucha en loopback, puerto 8080, e indica que hay cambios. La campana recupera los avisos por HTTP con la sesión del usuario. La creación vía `api/crear_notificacion.php` requiere `POST`, sesión de Administrador y el token CSRF en el cuerpo o en `X-CSRF-Token`.

El tiempo real público requiere un servicio WebSocket propio y configuración WSS.
