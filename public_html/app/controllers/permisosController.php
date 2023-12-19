<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\IViews;

class permisosController extends mainModel implements IViews
{
  public function getPermisosCargoId()
  {
    $tiposUsuarios = [
      1 => 'administrador',
      2 => 'almacenero',
      3 => 'vendedor',
      4 => 'cajero'
    ];

    $permisosSecciones = [
      $tiposUsuarios[1] => [
        'proformas_venta',
        'clientes',
        'inventario',
        'productos',
        'ordenes_compra',
        'trabajadores',
        'usuarios',
        'otros'
      ],

      $tiposUsuarios[2] => [
        'inventario',
        'productos',
        'ordenes_compra'
      ],

      $tiposUsuarios[3] => [
        'proformas_venta',
        'clientes'
      ],

      $tiposUsuarios[4] => [
        'proformas_venta',
        'clientes'
      ]
    ];

    $tipoUsuario = (isset($tiposUsuarios[$_SESSION['cargoId']]))
      ? $tiposUsuarios[$_SESSION['cargoId']]
      : null;

    if (is_null($tipoUsuario)) return [];

    $permisosUsuario = $permisosSecciones[$tipoUsuario];

    foreach ($permisosSecciones[$tipoUsuario] as $permisoSeccion) {
      if (isset(self::VISTAS[$permisoSeccion])) {
        $permisosUsuario = array_merge(
          $permisosUsuario,
          self::VISTAS[$permisoSeccion]
        );
      }
    }

    return $permisosUsuario;

    // $permisosUsuario = [
    //   $usuarios[1] => [],
    //   $usuarios[2] => [],
    //   $usuarios[3] => [],
    //   $usuarios[4] => []
    // ];

    // foreach ($permisosSecciones as $tipoUsuario => $permisosSeccion) {
    //   foreach ($permisosSeccion as $permisoSeccion) {
    //     $permisosUsuario[$tipoUsuario] = array_merge(
    //       $permisosUsuario[$tipoUsuario],
    //       self::VISTAS[$permisoSeccion]
    //     );
    //   }
    // }

    // $permisos = [
    //   1 => $permisosUsuario['administrador'],
    //   2 => $permisosUsuario['almacenero'],
    //   3 => $permisosUsuario['vendedor'],
    //   4 => $permisosUsuario['cajero'],
    // ];

    // return $permisos[$_SESSION['cargoId']];
  }
}
