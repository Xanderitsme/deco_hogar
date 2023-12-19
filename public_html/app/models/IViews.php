<?php

namespace app\models;

interface IViews
{
  const LISTA_BLANCA = [
    "dashboard",
    "logout"
  ];

  const VISTAS = [
    'proformas_venta' => [
      "listaProformasVenta",
      "nuevaProformaVenta"
    ],

    'clientes' => [
      "listaClientes",
      "nuevoCliente"
    ],

    'inventario' => [
      "listaMovimientosInventario"
    ],

    'productos' => [
      "listaProductos",
      "nuevoProducto"
    ],

    'ordenes_compra' => [
      "listaOrdenesCompra",
      "nuevaOrdenCompra"
    ],

    'trabajadores' => [
      "listaTrabajadores",
      "nuevoTrabajador",
      "actualizarTrabajador"
    ],

    'usuarios' => [
      "listaUsuarios",
      "nuevoUsuario",
      "actualizarUsuario"
    ],

    'otros' => []
  ];
}
