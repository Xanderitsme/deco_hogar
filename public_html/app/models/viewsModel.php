<?php

namespace app\models;

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

        if (in_array($vista, $listaBlanca)) {
            if (is_file("./app/views/content/" . $vista . "-view.php")) {
                $contenido = "./app/views/content/" . $vista . "-view.php";
            } else {
                $contenido = "404";
            }
        } elseif ($vista == "login" || $vista == "index") {
            $contenido = "login";
        } else {
            $contenido = "404";
        }

        return $contenido;
    }
}
