<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\usuarioController;

if (isset($_POST["modulo_usuario"])) {

    $insUsuario = new usuarioController();

    if ($_POST["modulo_usuario"] == "registrar") {
        echo $insUsuario->registrarUsuarioControlador();
    }

    if ($_POST["modulo_usuario"] == "eliminar") {
        echo $insUsuario->eliminarUsuarioControlador();
    }


} elseif (isset($_POST["modulo_trabajador"])) {
    $insUsuario = new usuarioController();

    if ($_POST["modulo_trabajador"] == "registrar") {
        echo $insUsuario->registrarTrabajadorControlador();
    }

    if ($_POST["modulo_trabajador"] == "eliminar") {
        echo $insUsuario->eliminarTrabajadorControlador();
    }

} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}