<?php

namespace app\models;

use app\models\mainModel;

interface ProformaVenta extends Objeto
{
  const NOMBRE_TABLA = "proformas_venta";
  const CAMPOS_TABLA = [
    "total",
    "fecha",
    "clienteID",
    "vendedorID",
    "estado"
  ];
}

class proformaVentaModel extends mainModel implements ProformaVenta
{
  public function registrar($datos)
  {
    $datosProformaVenta = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosProformaVenta))
      return $this->crearAlertaError("Ocurrió un error al procesar los datos de la proforma de venta, por favor intente más tarde");

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosProformaVenta);

    if (is_null($registrar))
      return $this->crearAlertaError("No se pudo registrar la proforma de venta, por favor intente más tarde");

    if ($registrar->rowCount() == 0)
      return $this->crearAlertaError("No se pudo registrar la proforma de venta, por favor intente nuevamente");

    return $this->crearAlertaLimpiarSuccess("Proforma de venta registrada", "La proforma " . $datos[0] . " ha sido registrado exitosamente");
  }

  public function obtener($id)
  {
  }

  public function actualizar($id, $datos)
  {
  }

  public function eliminar($id)
  {
  }

  public function obtenerProductosProformaVenta($id)
  {
    $consulta_datos = "
      select
        productos.nombre,
        detalles.ID,
        detalles.cantidad,
        detalles.subtotal 
      from proformas_venta
      join detalles on detalles.proforma_ventaID = proformas_venta.ID
      join productos on detalles.productoID = productos.ID
      where proformas_venta.ID = $id
      order by detalles.ID desc";

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) {
      return null;
    }

    return $datos->fetchAll();
  }

  public function detalleProductoAgregado($id, $productoId)
  {
    $consulta_detalle = "
      select 
        detalles.ID
      from proformas_venta
      join detalles on detalles.proforma_ventaID = proformas_venta.ID
      where proformas_venta.ID = $id
      and detalles.productoID = $productoId";

    $detalleId = $this->ejecutarConsulta($consulta_detalle);

    if (is_null($detalleId)) {
      return null;
    }
  
    return ($detalleId->rowCount() > 0) 
      ? $detalleId->fetchColumn() 
      : 0;
  }
}
