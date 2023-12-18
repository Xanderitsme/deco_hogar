<?php

namespace app\models;

use app\models\mainModel;

interface Producto extends Objeto
{
  const NOMBRE_TABLA = "productos";
  const CAMPOS_TABLA = [
    "nombre",
    "descripcion",
    "precio_venta",
    "precio_compra",
    "stock"
  ];
}

class productModel extends mainModel implements Producto
{
  public function registrar($datos)
  {
    $datosProducto = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosProducto))
      return $this->crearAlertaError("Ocurrió un error al procesar los datos del producto, por favor intente más tarde");

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosProducto);

    if (is_null($registrar))
      return $this->crearAlertaError("No se pudo registrar el producto, por favor intente más tarde");

    if ($registrar->rowCount() == 0)
      return $this->crearAlertaError("No se pudo registrar el producto, por favor intente nuevamente");

    return $this->crearAlertaLimpiarSuccess("Producto registrado", "El producto " . $datos[0] . " ha sido registrado exitosamente");
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

  public function obtenerListaProductosVenta($busqueda)
  {
    if (isset($busqueda) && !empty($busqueda)) {
      $consulta_datos = "
        select
          ID, 
          nombre, 
          descripcion, 
          precio_venta, 
          stock
        from productos 
        where 
          nombre like '%" . $busqueda . "%',
          or descripcion like '%" . $busqueda . "%'
        order by nombre asc";
    } else {
      $consulta_datos = "
        select
          ID, 
          nombre, 
          descripcion, 
          precio_venta, 
          stock
        from productos 
        order by nombre asc";
    }

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) {
      return null;
    }

    return $datos->fetchAll();
  }

  public function obtenerTotalProductos($busqueda)
  {
    if (isset($busqueda) && !empty($busqueda)) {
      $consulta_total = "
        select count(*) as total from productos
        where 
          nombre like '%" . $busqueda . "%',
          or descripcion like '%" . $busqueda . "%'
      ";
    } else {
      $consulta_total = "select count(*) as total from productos";
    }

    $total = $this->ejecutarConsulta($consulta_total);

    if (is_null($total)) {
      return null;
    }

    return $total->fetchColumn();
  }
}
