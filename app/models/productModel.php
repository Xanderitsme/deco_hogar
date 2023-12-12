<?php

namespace app\models;

use app\models\mainModel;

class productModel extends mainModel
{
  private const NOMBRE_TABLA = "productos";
  private const CAMPOS_TABLA = [
    "nombre", 
    "descripcion", 
    "precio_venta", 
    "precio_compra", 
    "stock"
  ];

  public function registrarProducto($nombre, $descripcion, $precioVenta, $precioCompra, $stock)
  {
    $datosProducto = [
      [
        "campo_nombre" => self::CAMPOS_TABLA[0],
        "campo_marcador" => ":" . self::CAMPOS_TABLA[0],
        "campo_valor" => $nombre
      ],
      [
        "campo_nombre" => self::CAMPOS_TABLA[1],
        "campo_marcador" => ":" . self::CAMPOS_TABLA[1],
        "campo_valor" => $descripcion
      ],
      [
        "campo_nombre" => self::CAMPOS_TABLA[2],
        "campo_marcador" => ":" . self::CAMPOS_TABLA[2],
        "campo_valor" => $precioVenta
      ],
      [
        "campo_nombre" => self::CAMPOS_TABLA[3],
        "campo_marcador" => ":" . self::CAMPOS_TABLA[3],
        "campo_valor" => $precioCompra
      ],
      [
        "campo_nombre" => self::CAMPOS_TABLA[4],
        "campo_marcador" => ":" . self::CAMPOS_TABLA[4],
        "campo_valor" => $stock
      ]
    ];

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosProducto);

    if (!is_null($registrar)) {
      if ($registrar->rowCount() == 1) {
        $alerta = $this->crearAlertaLimpiarSuccess("Producto registrado", "El producto " . $nombre . " ha sido registrado exitosamente");
      } else {
        $alerta = $this->crearAlertaError("No se pudo registrar el producto, por favor intente nuevamente");
      }
    } else {
      $alerta = $this->crearAlertaError("No se pudo registrar el producto, por favor intente más tarde");
    }

    return $alerta;
  }

  public function obtenerProducto()
  {
  }

  public function actualizarProducto()
  {
  }

  public function eliminarProducto()
  {
  }
}
