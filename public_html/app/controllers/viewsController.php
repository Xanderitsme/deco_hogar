<?php

namespace app\controllers;

use app\models\viewsModel;

class viewsController extends viewsModel
{
    public function obtenerVistasControlador($vista)
    {        
        if ($vista != "") {
            $respuesta = $this->obtenerVistasModelo($vista);
        } else {
            $respuesta = "login"; 
        }
        
        if ($respuesta == "login" && isset($_SESSION['id']) && isset($_SESSION['trabajadorId']) && !empty($_SESSION['id']) && !empty($_SESSION['trabajadorId'])) {
            $respuesta = $this->obtenerVistasModelo('dashboard');
        }
        
        return $respuesta;
    }
}
