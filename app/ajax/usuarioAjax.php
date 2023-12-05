<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\usuarioController;

if (isset($_POST["nuevo_usuario"])) {

    $insUsuario = new usuarioController();

    if ($_POST["nuevo_usuario"] == "registrar") {
        echo $insUsuario->registrarUsuarioControlador();
    }

} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}