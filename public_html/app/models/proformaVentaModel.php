<?php

namespace app\models;

use app\models\mainModel;

class proformaVentaModel extends mainModel
{
  private const NOMBRE_TABLA = "proformas_venta";
  private const CAMPOS_TABLA = [
    "total",
    "fecha",
    "clienteID",
    "vendedorID",
    "estado"
  ];

  public function registrarProformaVenta($nombre, $descripcion, $precioVenta, $precioCompra, $stock)
  {
    $datos = [$nombre, $descripcion, $precioVenta, $precioCompra, $stock];

    $datosProducto = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosProducto)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al procesar los datos del producto, por favor intente más tarde");
      return $alerta;
    }

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosProducto);

    if (is_null($registrar)) {
      $alerta = $this->crearAlertaError("No se pudo registrar el producto, por favor intente más tarde");
      return $alerta;
    }

    if ($registrar->rowCount() == 1) {
      $alerta = $this->crearAlertaLimpiarSuccess("Producto registrado", "El producto " . $nombre . " ha sido registrado exitosamente");
    } else {
      $alerta = $this->crearAlertaError("No se pudo registrar el producto, por favor intente nuevamente");
    }

    return $alerta;
  }

  public function obtenerProductosProformaVenta($id)
  {
    $consulta_datos = "
      select
        productos.nombre, 
        detalles.subtotal 
      from proformas_venta
      join detalles on detalles.proforma_ventaID = proformas_venta.ID
      join productos on detalles.productoID = productos.ID
      where proformas_venta.ID = $id";

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) {
      return null;
    }
  
    return $datos->fetchAll();
  }
}
