<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\productModel;
use app\models\proformaVentaModel;
use app\models\detalleModel;

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

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\- ]{1,100}", $nombre)) {
      $alerta = $this->crearAlertaError("El nombre no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,4}", $stock)) {
      $alerta = $this->crearAlertaError("El stock no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("^\d{1,5}(?:\.\d{1,2})?$", $precioVenta)) {
      $alerta = $this->crearAlertaError("El precio de venta no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("^\d{1,5}(?:\.\d{1,2})?$", $precioCompra)) {
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
          <span>Stock</span>
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
            <span>' . (($rows['stock'] > 0) ? $rows['stock'] : 'No disponible') . '</span>
            <span>' . MONEY_SYMBOL . $rows['precio_venta'] . '</span>

            <form class="FormularioSinConfirmacion" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="agregar">
              <input type="hidden" name="producto_id" value="' . $rows['ID'] . '">
              <input type="hidden" name="proforma_id" value="' . 1 . '">
              <button type="submit" class="button is-success is-rounded is-small">Agregar</button>
            </form>
          </div>';
      }
    } else {
      $tabla .= '
          <div class="detalles-producto">
            <span>No hay productos registrados en el sistema</span>
          </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  public function obtenerTotalProductos($busqueda)
  {
    $insProd = new productModel();

    return $insProd->obtenerTotalProductos($busqueda);
  }

  public function listarProductosProformaControlador($proformaId)
  {
    $proformaId = $this->limpiarCadena($proformaId);

    $insProformaVenta = new proformaVentaModel();

    $datos = $insProformaVenta->obtenerProductosProformaVenta($proformaId);

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
          <span>Producto</span>
          <span>Cantidad</span>
          <span>Subtotal</span>
          <span>Opciones</span>
        </nav>
    ';

    if ($total >= 1) {
      foreach ($datos as $rows) {
        $tabla .= '
          <div class="prod-sel">
            <span>' . $rows['nombre'] . '</span>

            <form class="FormularioSinConfirmacion" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="actualizarDetalle">
              <input type="hidden" name="detalle_id" value="' . $rows['ID'] . '">
              <input class="input" name="detalle_cantidad" type=text value=' . $rows['cantidad'] . ' style="border: none;">
            </form>

            <span>' . MONEY_SYMBOL . $rows['subtotal'] . '</span>

            <form class="FormularioSinConfirmacion" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="disminuirDetalle">
              <input type="hidden" name="detalle_id" value="' . $rows['ID'] . '">
              <button type="submit" class="button is-warning is-rounded is-small">Disminuir</button>
            </form>
            <form class="FormularioSinConfirmacion" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="eliminarDetalle">
              <input type="hidden" name="detalle_id" value="' . $rows['ID'] . '">
              <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
            </form>
          </div>';
      }
    } else {
      $tabla .= '
        <div class="prod-sel">
          <span>No hay ningún producto seleccionado</span>
        </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  public function agregarProductoVentaControlador()
  {
    $productoId = $this->limpiarCadena($_POST["producto_id"]);
    $proformaId = $this->limpiarCadena($_POST["proforma_id"]);

    $insProd = new productModel();

    $datosProducto = $insProd->obtener($productoId);

    if (is_null($datosProducto)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al intentar agregar el producto, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    $insDetalle = new detalleModel();

    $insProformaVenta = new proformaVentaModel();

    $detalleId = $insProformaVenta->detalleProductoAgregado($proformaId, $productoId);

    if (is_null($detalleId)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al intentar agregar el producto, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    if ($datosProducto['stock'] < 1) {
      $alerta = $this->crearAlertaError("No hay stock suficiente para agregar este producto, por favor informe al área de almanén");
      return json_encode($alerta);
      exit();
    }

    if ($detalleId != 0) {
      $datosDetalle = $insDetalle->obtener($detalleId);

      if (is_null($datosDetalle)) {
        $alerta = $this->crearAlertaError("Ocurrió un error al intentar agregar el producto, por favor vuelva a intentarlo");
        return json_encode($alerta);
        exit();
      }

      $cantidad = $datosDetalle['cantidad'] + 1;
      $subtotal = $cantidad * $datosDetalle['precio_venta'];

      $actualizaDetalle = $insDetalle->actualizar($detalleId, [$cantidad, $subtotal, null, null]);

      if (!$actualizaDetalle) {
        $alerta = $this->crearAlertaError("Ocurrió un error al intentar agregar el producto, por favor vuelva a intentarlo");
        return json_encode($alerta);
        exit();
      }
    } else {
      $subtotal = $datosProducto['precio_venta'] * 1;

      $registroDetalle = $insDetalle->registrar([1, $subtotal, $productoId, $proformaId]);

      if (!$registroDetalle) {
        $alerta = $this->crearAlertaError("Ocurrió un error al intentar agregar el producto, por favor vuelva a intentarlo");
        return json_encode($alerta);
        exit();
      }
    }

    $alerta = $this->crearAlertaRecargar("Producto agregado", "El producto ha sido agregado a la proforma de venta con éxito");
    return json_encode($alerta);
  }

  public function eliminarProductoVentaControlador()
  {
    $detalleId = $this->limpiarCadena($_POST["detalle_id"]);

    $insDetalle = new detalleModel();

    $eliminar = $insDetalle->eliminar($detalleId);

    if (!$eliminar) {
      $alerta = $this->crearAlertaError("Ocurrió un error al intentar eliminar el detalle, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    $alerta = $this->crearAlertaRecargar("Detalle eliminado", "El detalle ha sido removido de la proforma de venta con éxito");
    return json_encode($alerta);
  }

  public function disminuirProductoVentaControlador()
  {
    $detalleId = $this->limpiarCadena($_POST["detalle_id"]);

    $insDetalle = new detalleModel();

    $datosDetalle = $insDetalle->obtener($detalleId);

    if (is_null($datosDetalle)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al intentar disminuir la cantidad, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    if ($datosDetalle['cantidad'] == 1) {
      $eliminar = $insDetalle->eliminar($detalleId);

      if (!$eliminar) {
        $alerta = $this->crearAlertaError("Ocurrió un error al intentar disminuir la cantidad, por favor vuelva a intentarlo");
        return json_encode($alerta);
        exit();
      }

      $alerta = $this->crearAlertaRecargar("Proforma modificada", "La cantidad del producto ha sido disminuida con éxito");
      return json_encode($alerta);
    }

    $cantidad = $datosDetalle['cantidad'] - 1;
    $subtotal = $cantidad * $datosDetalle['precio_venta'];

    $actualizaDetalle = $insDetalle->actualizar($detalleId, [$cantidad, $subtotal, null, null]);

    if (!$actualizaDetalle) {
      $alerta = $this->crearAlertaError("Ocurrió un error al modificar la proforma, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    $alerta = $this->crearAlertaRecargar("Proforma modificada", "La cantidad del producto ha sido disminuida con éxito");
    return json_encode($alerta);
  }

  public function actualizarDetalleVentaControlador()
  {
    $detalleId = $this->limpiarCadena($_POST["detalle_id"]);
    $cantidad = $this->limpiarCadena($_POST["detalle_cantidad"]);

    $insDetalle = new detalleModel();

    $datosDetalle = $insDetalle->obtener($detalleId);

    if (is_null($datosDetalle)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al intentar disminuir la cantidad, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,4}", $cantidad)) {
      $alerta = $this->crearAlertaError("Ingrese una cantidad válida");
      return json_encode($alerta);
      exit();
    }

    if ($cantidad == 0) {
      $eliminar = $insDetalle->eliminar($detalleId);

      if (!$eliminar) {
        $alerta = $this->crearAlertaError("Ocurrió un error al intentar cambiar la cantidad, por favor vuelva a intentarlo");
        return json_encode($alerta);
        exit();
      }

      $alerta = $this->crearAlertaRecargar("Proforma modificada", "La cantidad del producto ha sido modificada con éxito");
      return json_encode($alerta);
    }

    if ($cantidad < 0) {
      $alerta = $this->crearAlertaRecargarError("Ocurrió un error inesperado", "No se puede ingresar una cantidad menor a cero");
      return json_encode($alerta);
      exit();
    }

    if ($datosDetalle['stock'] + $datosDetalle['cantidad'] < $cantidad) {
      $alerta = $this->crearAlertaRecargarError("Ocurrió un error inesperado", "No se pudo modificar la proforma, la cantidad solicitada supera el stock disponible");
      return json_encode($alerta);
      exit();
    }

    $subtotal = $cantidad * $datosDetalle['precio_venta'];

    $actualizaDetalle = $insDetalle->actualizar($detalleId, [$cantidad, $subtotal, null, null]);

    if (!$actualizaDetalle) {
      $alerta = $this->crearAlertaError("Ocurrió un error al modificar la proforma, por favor vuelva a intentarlo");
      return json_encode($alerta);
      exit();
    }

    $alerta = $this->crearAlertaRecargar("Proforma modificada", "La cantidad del producto ha sido modificada con éxito");
    return json_encode($alerta);
  }

  private function errorRegistroProducto()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del producto");
    return json_encode($alerta);
  }
}
