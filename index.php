<?php

require_once __DIR__ . "/includes/app.php";

iniciar_sesion();
redirigir(empty($_SESSION["usuario_id"]) ? "login.php" : "dashboard.php");
