<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\IViews;
use PDO;

class loginController extends mainModel implements IViews
{
  public function iniciarSesionControlador()
  {
    $usuario = $this->limpiarCadena($_POST['login_usuario']);
    $clave = $this->limpiarCadena($_POST['login_clave']);

    if (empty($usuario) || empty($clave)) {
      echo $this->errorLogin("No has llenado todos los campos");
      return;
    }

    if ($this->verificarDatos("[a-zA-Z0-9]{1,20}", $usuario)) {
      echo $this->errorLogin("Usuario o clave incorrectos");
      return;
    }

    if ($this->verificarDatos("[a-zA-Z0-9$@._\-]{1,20}", $clave)) {
      echo $this->errorLogin("Usuario o clave incorrectos");
      return;
    }

    $checkUsuario = $this->ejecutarConsulta("select * from cuentas where usuario = '$usuario'");

    if (is_null($checkUsuario)) {
      echo $this->errorLogin("Ha ocurrido un error al intentar verificar los datos de inicio de sesión");
      return;
    }

    if ($checkUsuario->rowCount() == 0) {
      echo $this->errorLogin("El usuario $usuario no está registrado");
      return;
    }

    $datosUsuario = $checkUsuario->fetch(PDO::FETCH_ASSOC);

    if ($datosUsuario['usuario'] != $usuario || !password_verify($clave, $datosUsuario['clave'])) {
      echo $this->errorLogin("Clave incorrecta");
      return;
    }

    $_SESSION['id'] = $datosUsuario['ID'];
    $_SESSION['trabajadorId'] = $datosUsuario['trabajadorID'];
    $_SESSION['usuario'] = $datosUsuario['usuario'];
    $_SESSION['foto'] = $datosUsuario['foto'];

    $trabajadorId = $datosUsuario['trabajadorID'];
    $datosTrabajador = $this->ejecutarConsulta("
      select 
        trabajadores.nombres, 
        trabajadores.apellidos, 
        trabajadores.cargoID,
        cargos.nombre_cargo 
      from trabajadores 
      join cargos on cargos.ID = trabajadores.cargoID
      where trabajadores.ID = $trabajadorId
    ");

    if (is_null($datosTrabajador)) {
      echo $this->errorLogin("Ha ocurrido un error al intentar cargar los datos del trabajador, intentelo de nuevo");
      return;
    }

    $datosTrabajador = $datosTrabajador->fetch(PDO::FETCH_ASSOC);

    $_SESSION['nombres'] = $datosTrabajador['nombres'];
    $_SESSION['apellidos'] = $datosTrabajador['apellidos'];
    $_SESSION['cargo'] = $datosTrabajador['nombre_cargo'];
    $_SESSION['cargoId'] = $datosTrabajador['cargoID'];

    if (headers_sent()) {
      echo "
        <script>
          window.location.href = '" . APP_URL . "dashboard/';
        </script>
      ";
    } else {
      header("Location: " . APP_URL . "dashboard/");
    }
  }

  public function cerrarSesionControlador()
  {
    session_destroy();

    if (headers_sent()) {
      echo "
        <script>
          window.location.href = '" . APP_URL . "login/';
        </script>
      ";
    } else {
      header("Location: " . APP_URL . "login/");
    }
  }

  private function errorLogin($mensajeAlerta)
  {
    return "
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Ocurrió un error inesperado',
          text: '" . $mensajeAlerta . "',
          confirmButtonText: 'Aceptar'
        });
      </script>
    ";
  }

  public function permisoAccesoVista($vista)
  {
    $extra = ["./app/views/content/", "-view.php"];

    $vista = str_replace($extra, '', $vista);

    $permisosGenerales = [
      "dashboard",
      "logout",
      "actualizarUsuario"
    ];

    if (in_array($vista, $permisosGenerales)) return true;

    $insPermisos = new permisosController();

    return in_array($vista, $insPermisos->getPermisosCargoId());
  }
}
