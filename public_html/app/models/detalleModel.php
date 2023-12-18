<?php

namespace app\models;

use app\models\mainModel;
use PDO;

interface Detalle extends Objeto
{
  const NOMBRE_TABLA = "detalles";
  const CAMPOS_TABLA = [
    "cantidad",
    "subtotal",
    "unidad",
    "productoID",
    "proforma_ventaID"
  ];
}

class detalleModel extends mainModel implements Detalle
{
  public function registrar($datos)
  {
    $datosDetalle = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosDetalle))
      return false;

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosDetalle);

    if (is_null($registrar))
      return false;

    if ($registrar->rowCount() == 0)
      return false;

    return true;
  }

  public function obtener($id)
  {
    $consulta_datos = "
      select
        detalles.cantidad,
        detalles.subtotal,
        detalles.unidad,
        productos.nombre,
        detalles.proforma_ventaID
      from detalles
      inner join productos on detalles.productoID = productos.ID
      where detalles.ID = $id
    ";

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) return null;

    return $datos->fetch(PDO::FETCH_ASSOC);
  }

  public function actualizar($id, $datos)
  {
    
  }

  public function eliminar($id)
  {
  }
}
