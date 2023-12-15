<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\trabajadorController;


if (isset($_POST["modulo_trabajador"])) {
  $insTrabajador = new trabajadorController();

  if ($_POST["modulo_trabajador"] == "registrar") {
    echo $insTrabajador->registrarTrabajadorControlador();
  }

  if ($_POST["modulo_trabajador"] == "eliminar") {
    echo $insTrabajador->eliminarTrabajadorControlador();
  }
} else {
  session_destroy();
  header("Location: " . APP_URL . "login/");
}
