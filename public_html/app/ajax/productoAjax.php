<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\productController;

if (isset($_POST["modulo_producto"])) {
  $insProducto = new productController();

  if ($_POST["modulo_producto"] == "registrar") {
    echo $insProducto->registrarProductoControlador();
  }
} else {
  session_destroy();
  header("Location: " . APP_URL . "login/");
}