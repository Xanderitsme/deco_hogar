<?php

namespace app\models;

use app\controllers\loginController;

class viewsModel
{
    protected function obtenerVistasModelo($vista)
    {
        $listaBlanca = [
            "dashboard",
            "logout"
        ];

        $proformasVenta = [
            "listaProformasVenta",
            "nuevaProformaVenta"
        ];

        $clientes = [
            "listaClientes",
            "nuevoCliente"
        ];

        $productos = [
            "listaProductos",
            "nuevoProducto"
        ];

        $inventario = ["listaMovimientosInventario"];

        $ordenesCompra = [
            "listaOrdenesCompra",
            "nuevaOrdenCompra"
        ];

        $trabajadores = [
            "listaTrabajadores",
            "nuevoTrabajador",
            "actualizarTrabajador"
        ];

        $usuarios = [
            "listaUsuarios",
            "nuevoUsuario",
            "actualizarUsuario"
        ];

        $listaBlanca = array_merge(
            $listaBlanca,
            $proformasVenta,
            $clientes,
            $productos,
            $inventario,
            $ordenesCompra,
            $trabajadores,
            $usuarios
        );

        $insLogin = new loginController();

        if (in_array($vista, $listaBlanca)) {
            if (is_file("./app/views/content/" . $vista . "-view.php")) {
                if ($insLogin->permisoAccesoVista($vista)) {
                    $contenido = "./app/views/content/" . $vista . "-view.php";
                } else {
                    $contenido = "403";
                }
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
