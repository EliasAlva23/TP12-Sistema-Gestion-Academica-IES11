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

1. Instalar XAMPP.
2. Copiar el proyecto dentro de:

   `C:\xampp\htdocs\`

3. Iniciar Apache y MySQL desde XAMPP.
4. Importar:

   `database/instituto.sql`

   desde phpMyAdmin.

5. Verificar la configuración de:

   `config/conexion.php`

6. Abrir:

   `http://localhost/TP12_IES11_Final/`

## WebSocket

Para iniciar el servidor de notificaciones abrir CMD y ejecutar:

```bash
cd C:\xampp\htdocs\TP12_IES11_Final\websocket
C:\xampp\php\php.exe servidor.php