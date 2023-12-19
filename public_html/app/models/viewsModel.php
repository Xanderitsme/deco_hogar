<?php

namespace app\models;

class viewsModel implements IViews
{
    protected function obtenerVistasModelo($vista)
    {
        $listaBlanca = self::LISTA_BLANCA;

        foreach (self::VISTAS as $vistaSeccion) {
            $listaBlanca = array_merge(
                $listaBlanca,
                $vistaSeccion
            );
        }

        // $listaBlanca = array_merge(
        //     self::LISTA_BLANCA,
        //     self::VISTAS['proformas_venta'],
        //     self::VISTAS['clientes'],
        //     self::VISTAS['inventario'],
        //     self::VISTAS['productos'],
        //     self::VISTAS['ordenes_compra'],
        //     self::VISTAS['trabajadores'],
        //     self::VISTAS['usuarios'],
        //     self::VISTAS['otros']
        // );

        if (in_array($vista, $listaBlanca)) {
            if (is_file("./app/views/content/" . $vista . "-view.php")) {
                $contenido = "./app/views/content/" . $vista . "-view.php";
            } else {
                $contenido = "404";
            }
        } elseif ($vista == "login" || $vista == "index") {
            $contenido = "login";
        } elseif ($vista == "403") {
            $contenido = "403";
        } else {
            $contenido = "404";
        }

        return $contenido;
    }
}
