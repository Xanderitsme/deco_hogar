<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\productModel;
use app\models\proformaVentaModel;

class productController extends mainModel
{
  public function registrarProductoControlador()
  {
    $nombre = $this->limpiarCadena($_POST["producto_nombre"]);
    $stock = $this->limpiarCadena($_POST["producto_stock"]);
    $precioVenta = $this->limpiarCadena($_POST["producto_precio_venta"]);
    $precioCompra = $this->limpiarCadena($_POST["producto_precio_compra"]);
    $descripcion = $this->limpiarCadena($_POST["producto_descripcion"]);

    if (empty($nombre) || empty($descripcion) || empty($precioVenta) || empty($precioCompra) || empty($stock)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}", $nombre)) {
      $alerta = $this->crearAlertaError("El nombre no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}", $stock)) {
      $alerta = $this->crearAlertaError("El stock no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}\.[0-9]{2}", $precioVenta)) {
      $alerta = $this->crearAlertaError("El precio de venta no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}\.[0-9]{2}", $precioCompra)) {
      $alerta = $this->crearAlertaError("El precio de compra no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{5,250}", $descripcion)) {
      $alerta = $this->crearAlertaError("La descripción no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    $check_nombre = $this->ejecutarConsulta("SELECT nombre from productos where nombre = '$nombre'");

    if (is_null($check_nombre)) {
      return $this->errorRegistroProducto();
      exit();
    }

    if ($check_nombre->rowCount() > 0) {
      $alerta = $this->crearAlertaError("El nombre del producto que acaba de ingresar ya se encuentra registrado");
      return json_encode($alerta);
      exit();
    }

    $insProducto = new productModel();

    $alerta = $insProducto->registrar([$nombre, $descripcion, $precioVenta, $precioCompra, $stock]);

    return json_encode($alerta);
  }

  public function listarProductosVentaControlador($busqueda)
  {
    $busqueda = $this->limpiarCadena($busqueda);

    $tabla = "";

    $insProducto = new productModel();

    $datos = $insProducto->obtenerListaProductosVenta($busqueda);

    if (is_null($datos)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar los datos de los productos");
      return;
    }

    $total = $insProducto->obtenerTotalProductos($busqueda);

    if (is_null($total)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar el total de productos");
      return;
    }

    $tabla .= '
      <div class="tabla borde sombra">
        <nav class="nav-productos">
          <span>Producto</span>
          <span>Estado</span>
          <span>Precio</span>
          <span>Agregar</span>
        </nav>
    ';

    if ($total >= 1) {
      foreach ($datos as $rows) {
        $tabla .= '
          <div class="detalles-producto">
            <span>
              <h3 style="font-weight: 600">' . $rows['nombre'] . '<br></h3>
              <p style="font-size: 0.9em">
                ' . $rows['descripcion'] . '
              </p>
            </span>
            <span>' . (($rows['stock'] > 0) ? 'Disponible' : 'No disponible') . '</span>
            <span>' . MONEY_SYMBOL . $rows['precio_venta'] . '</span>

            <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="agregar">
              <input type="hidden" name="producto_id" value="' . $rows['ID'] . '">
              <button type="submit" class="button is-success is-rounded is-small">Agregar</button>
            </form>
          </div>';
      }
    } else {
      $tabla .= '
          <div class="detalles-producto">
            <span>No hay registros en el sistema</span>
          </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  public function listarProductosProformaControlador($id)
  {
    $id = $this->limpiarCadena($id);

    $insProformaVenta = new proformaVentaModel();

    $datos = $insProformaVenta->obtenerProductosProformaVenta($id);

    if (is_null($datos)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar los datos de la proforma de venta");
      return;
    }

    $totalProforma = 0;

    foreach ($datos as $rows) {
      $totalProforma += $rows['subtotal'];
    }

    $total = sizeof($datos);

    $tabla = "";

    $tabla .= '
      <div class="tabla borde sombra">
        <nav class="nav-venta">
          <span>Total: ' . MONEY_SYMBOL . $totalProforma . '</span>
          <button class="button is-info is-rounded is-normal">Generar proforma</button>
        </nav>
      </div>
      <div class="tabla borde sombra">
        <nav class="nav-prod-sel">
          <span>Nombre</span>
          <span>Opciones</span>
        </nav>
    ';

    if ($total >= 1) {
      foreach ($datos as $rows) {
        $tabla .= '
          <div class="prod-sel">
            <span>' . $rows['nombre'] . '</span>
            <button class="button is-success is-rounded is-small">Editar</button>
            <button class="button is-danger is-rounded is-small">Eliminar</button>
          </div>';
      }
    } else {
      $tabla .= '
        <div class="prod-sel">
          <span>No hay registros en el sistema</span>
        </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  private function errorRegistroProducto()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del producto");
    return json_encode($alerta);
  }
}
