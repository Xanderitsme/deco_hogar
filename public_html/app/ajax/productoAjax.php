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
  
  if ($_POST["modulo_producto"] == "agregar") {
    echo $insProducto->agregarProductoVentaControlador();
  }
  
  if ($_POST["modulo_producto"] == "eliminarDetalle") {
    echo $insProducto->eliminarProductoVentaControlador();
  }
  
  if ($_POST["modulo_producto"] == "disminuirDetalle") {
    echo $insProducto->disminuirProductoVentaControlador();
  }
  
  if ($_POST["modulo_producto"] == "actualizarDetalle") {
    echo $insProducto->actualizarDetalleVentaControlador();
  }
} else {
  session_destroy();
  header("Location: " . APP_URL . "login/");
}