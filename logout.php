<?php
/*
 * logout.php
 * Cierra la sesión del usuario de forma completa:
 * vacía los datos, elimina la cookie y destruye la sesión.
 */


session_start();

$_SESSION = [];

session_destroy();

header("Location: login.php");
exit;