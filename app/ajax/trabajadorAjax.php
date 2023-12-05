<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\trabajadorController;

if (isset($_POST["nuevo_trabajador"])) {

    $insTrabajador = new trabajadorController();

    if ($_POST["nuevo_trabajador"] == "registrar") {
        echo $insTrabajador->registrarTrabajadorControlador();
    }

} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}