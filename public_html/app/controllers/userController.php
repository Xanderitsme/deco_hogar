<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\trabajadorModel;
use app\models\userModel;
use PDO;

class userController extends mainModel
{
  public function registrarUsuarioControlador()
  {
    $trabajadorId = $this->limpiarCadena($_POST["usuario_trabajador_id"]);
    $usuario = $this->limpiarCadena($_POST["usuario_usuario"]);
    $clave1 = $this->limpiarCadena($_POST["usuario_clave_1"]);
    $clave2 = $this->limpiarCadena($_POST["usuario_clave_2"]);

    if (empty($trabajadorId) || empty($usuario) || empty($clave1) || empty($clave2)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,3}", $trabajadorId)) {
      $alerta = $this->crearAlertaError("El ID del trabajador no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
      $alerta = $this->crearAlertaError("El usuario no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9$@._\-]{5,20}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@._\-]{5,20}", $clave2)) {
      $alerta = $this->crearAlertaError("Las claves no coinciden con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($clave1 != $clave2) {
      $alerta = $this->crearAlertaError("Las claves que acaba de ingresar no coinciden");
      return json_encode($alerta);
      exit();
    }

    $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);

    $check_trabajadorId = $this->ejecutarConsulta("select ID from trabajadores where ID = $trabajadorId");

    if (is_null($check_trabajadorId)) {
      return $this->errorRegistroUsuario();
      exit();
    }

    if ($check_trabajadorId->rowCount() == 0) {
      $alerta = $this->crearAlertaError("El trabajador ingresado no esta registrado");
      return json_encode($alerta);
      exit();
    }

    $check_cuenta_registrada = $this->ejecutarConsulta("select trabajadorID from cuentas where trabajadorID = $trabajadorId");

    if (is_null($check_cuenta_registrada)) {
      return $this->errorRegistroUsuario();
      exit();
    }

    if ($check_cuenta_registrada->rowCount() > 0) {
      $alerta = $this->crearAlertaError("El trabajador ingresado ya tiene una cuenta registrada");
      return json_encode($alerta);
      exit();
    }

    $check_usuario = $this->ejecutarConsulta("select usuario from cuentas where usuario = '$usuario'");

    if (is_null($check_usuario)) {
      return $this->errorRegistroUsuario();
      exit();
    }

    if ($check_usuario->rowCount() > 0) {
      $alerta = $this->crearAlertaError("El nombre de usuario ingresado ya se encuentra registrado");
      return json_encode($alerta);
      exit();
    }

    $insUser = new userModel();

    if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {
      $foto = $insUser->guardarFoto($usuario);

      if (is_array($foto)) {
        $alerta = $foto;
        return json_encode($alerta);
        exit();
      }
    } else {
      $foto = "";
    }

    $alerta = $insUser->registrar([$trabajadorId, $usuario, $clave, $foto]);

    return json_encode($alerta);
  }

  public function listarUsuariosControlador($pagina, $registros, $url, $busqueda)
  {
    $pagina = $this->limpiarCadena($pagina);
    $registros = $this->limpiarCadena($registros);
    $url = $this->limpiarCadena($url);
    $url = APP_URL . $url . "/";
    $busqueda = $this->limpiarCadena($busqueda);

    $tabla = "";

    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
    $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

    if (isset($busqueda) && !empty($busqueda)) {
      $consulta_datos = "
                select
                    cuentas.ID,
                    cuentas.usuario,
                    trabajadores.nombres,
                    trabajadores.apellidos,
                    cargos.nombre_cargo
                from cuentas
                join trabajadores on cuentas.trabajadorID = trabajadores.ID
                join cargos on trabajadores.cargoID = cargos.ID
                where cuentas.ID <> " . $_SESSION['id'] . "
                and cuentas.ID <> 1 
                and (
                    trabajadores.nombres like '%" . $busqueda . "%'
                    or trabajadores.apellidos like '%" . $busqueda . "%'
                    or cargos.nombre_cargo like '%" . $busqueda . "%'
                    or cuentas.usuario like '%" . $busqueda . "%'
                )
                order by apellidos asc limit " . $inicio . ", " . $registros;

      $consulta_total = "
                select count(*) as total from cuentas 
                join trabajadores on cuentas.trabajadorID = trabajadores.ID
                join cargos on trabajadores.cargoID = cargos.ID
                where cuentas.ID <> " . $_SESSION['id'] . "
                and cuentas.ID <> 1
                and (
                    trabajadores.nombres like '%" . $busqueda . "%'
                    or trabajadores.apellidos like '%" . $busqueda . "%'
                    or cargos.nombre_cargo like '%" . $busqueda . "%'
                    or cuentas.usuario like '%" . $busqueda . "%'
                )";
    } else {
      $consulta_datos = "
                select
                    cuentas.ID,
                    cuentas.usuario,
                    trabajadores.nombres,
                    trabajadores.apellidos,
                    cargos.nombre_cargo
                from cuentas
                join trabajadores on cuentas.trabajadorID = trabajadores.ID
                join cargos on trabajadores.cargoID = cargos.ID
                where cuentas.ID <> " . $_SESSION['id'] . "
                and cuentas.ID <> 1 
                order by apellidos asc limit " . $inicio . ", " . $registros;

      $consulta_total = "
                select count(*) as total from cuentas 
                join trabajadores on cuentas.trabajadorID = trabajadores.ID
                join cargos on trabajadores.cargoID = cargos.ID
                where cuentas.ID <> " . $_SESSION['id'] . "
                and cuentas.ID <> 1";
    }

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar los usuarios");
      return;
    }

    $datos = $datos->fetchAll();

    $total = $this->ejecutarConsulta($consulta_total);

    if (is_null($total)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar el total de usuarios");
      return;
    }

    $total = (int) $total->fetch(PDO::FETCH_ASSOC)['total'];

    $numeroPaginas = ceil($total / $registros);

    $tabla .= '
        <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th class="has-text-centered">#</th>
                    <th class="has-text-centered">Usuario</th>
                    <th class="has-text-centered">Nombres y apellidos</th>
                    <th class="has-text-centered">Cargo</th>
                    <th class="has-text-centered" colspan="2">Opciones</th>
                </tr>
            </thead>
            <tbody>
        ';

    if ($total >= 1 && $pagina <= $numeroPaginas) {
      $contador = $inicio + 1;
      $pag_inicio = $inicio + 1;

      foreach ($datos as $rows) {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td>' . $contador . '</td>
                    <td>' . $rows['usuario'] . '</td>
                    <td>' . $rows['nombres'] . ' '  . $rows['apellidos'] . '</td>
                    <td>' . $rows['nombre_cargo'] . '</td>
                    <td>
                        <a href="' . APP_URL . 'actualizarUsuario/' . $rows['ID'] . '/" class="button is-success is-rounded is-small">Actualizar</a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

                            <input type="hidden" name="modulo_usuario" value="eliminar">
                            <input type="hidden" name="usuario_id" value="' . $rows['ID'] . '">

                            <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
                        </form>
                    </td>
                </tr>
                ';
        $contador++;
      }

      $pag_final = $contador - 1;
    } else {
      if ($total >= 1) {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td colspan="6">
                        <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </td>
                </tr>
                ';
      } else {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td colspan="6">
                        No hay registros en el sistema
                    </td>
                </tr>
                ';
      }
    }

    $tabla .= '</tbody></table></div>';

    if ($total >= 1 && $pagina <= $numeroPaginas) {
      $tabla .= '
            <p class="has-text-right">
                Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> 
                de un <strong>total de ' . $total . '</strong>
            </p>
            ';

      $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 10);
    }

    return $tabla;
  }

  public function eliminarUsuarioControlador()
  {
    $id = $this->limpiarCadena($_POST['usuario_id']);

    if ($id == 1) {
      $alerta = $this->crearAlertaError('No podemos eliminar la cuenta del usuario principal');
      return json_encode($alerta);
      exit();
    }

    $datos = $this->ejecutarConsulta("
            select 
                cuentas.usuario,
                cuentas.foto, 
                trabajadores.nombres,
                trabajadores.apellidos
            from cuentas
            join trabajadores on cuentas.trabajadorID = trabajadores.ID
            where cuentas.ID <> 1
            and cuentas.ID = $id
        ");

    if (is_null($datos)) {
      $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar cargar los datos del usuario');
      return json_encode($alerta);
      exit();
    }

    if ($datos->rowCount() == 0) {
      $alerta = $this->crearAlertaError('No pudimos encontrar ese usuario en el sistema');
      return json_encode($alerta);
      exit();
    }

    $datos = $datos->fetch(PDO::FETCH_ASSOC);

    $eliminarUsuario = $this->eliminarRegistro("cuentas", "ID", $id);

    if (is_null($eliminarUsuario)) {
      $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar eliminar el usuario');
      return json_encode($alerta);
      exit();
    }

    if ($eliminarUsuario->rowCount() == 1) {
      if (is_file("../views/fotos/" . $datos['foto'])) {
        chmod("../views/fotos/" . $datos['foto'], 0777);
        unlink("../views/fotos/" . $datos['foto']);
      }

      $alerta = $this->crearAlertaRecargar("Usuario eliminado", "El usuario " . $datos['usuario'] . " '" . $datos['nombres'] . " " . $datos['apellidos'] . "' se eliminó con éxito");
    } else {
      $alerta = $this->crearAlertaError("No se pudo eliminar el usuario "  . $datos['usuario'] . " '" . $datos['nombres'] . " " . $datos['apellidos'] . "', por favor intente nuevamente");
    }

    return json_encode($alerta);
  }

  public function actualizarUsuarioControlador()
  {
    $id = $this->limpiarCadena($_POST['usuario_id']);

    $insUser = new userModel();

    $datos = $insUser->obtener($id);

    if (is_null($datos)) {
      $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar cargar los datos del usuario');
      return json_encode($alerta);
      exit();
    }

    if ($datos == 0) {
      $alerta = $this->crearAlertaError('No pudimos encontrar ese usuario en el sistema');
      return json_encode($alerta);
      exit();
    }

    $admin_usuario = $this->limpiarCadena($_POST['administrador_usuario']);
    $admin_clave = $this->limpiarCadena($_POST['administrador_clave']);

    if (empty($admin_usuario) || empty($admin_clave)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios, correspondientes a su USUARIO y CLAVE como administrador");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $admin_usuario)) {
      $alerta = $this->crearAlertaError("Su usuario no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9$@._\-]{5,20}", $admin_clave)) {
      $alerta = $this->crearAlertaError("Su clave no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    $checkAdmin = $this->ejecutarConsulta("
      select * from cuentas 
      where usuario = '$admin_usuario'
      and ID = " . $_SESSION['id'] . "
    ");

    if (is_null($checkAdmin)) {
      $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar verificar sus datos de administrador");
      return json_encode($alerta);
      exit();
    }

    if ($checkAdmin->rowCount() == 0) {
      $alerta = $this->crearAlertaError("Las credenciales de acceso ingresadas no coinciden con la sesión actual");
      return json_encode($alerta);
      exit();
    }

    $datosAdmin = $checkAdmin->fetch(PDO::FETCH_ASSOC);

    if ($datosAdmin['usuario'] != $admin_usuario || !password_verify($admin_clave, $datosAdmin['clave'])) {
      $alerta = $this->crearAlertaError("Clave incorrecta");
      return json_encode($alerta);
      exit();
    }

    $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
    $cargoId = $this->limpiarCadena($_POST['trabajador_cargo']);
    $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
    $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

    if (empty($usuario) || empty($cargoId)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
      $alerta = $this->crearAlertaError("El usuario no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[1-9]{1,3}", $cargoId)) {
      $alerta = $this->crearAlertaError("El cargo no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($cargoId != $datos['cargoID'] && $_SESSION['cargoId'] == 1 && $id != 1) {
      $check_cargo = $this->ejecutarConsulta("select ID from cargos where ID = $cargoId");

      if (is_null($check_cargo)) {
        $alerta = $this->crearAlertaError("El cargo ingresado no existe");
        return json_encode($alerta);
        exit();
      }

      if ($check_cargo->rowCount() == 0) {
        $alerta = $this->crearAlertaError("El cargo ingresado no existe");
        return json_encode($alerta);
        exit();
      }
    } else {
      $cargoId = null;
    }

    if ($usuario != $datos['usuario']) {
      $check_usuario = $this->ejecutarConsulta("select usuario from cuentas where usuario = '$usuario'");

      if (is_null($check_usuario)) {
        $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar verificar los datos del usuario");
        return json_encode($alerta);
        exit();
      }

      if ($check_usuario->rowCount() > 0) {
        $alerta = $this->crearAlertaError("El nombre de usuario ingresado ya se encuentra registrado");
        return json_encode($alerta);
        exit();
      }
    } else {
      $usuario = null;
    }

    if (!empty($clave1) || !empty($clave2)) {
      if ($this->verificarDatos("[a-zA-Z0-9$@._\-]{5,20}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@._\-]{5,20}", $clave2)) {
        $alerta = $this->crearAlertaError("Las claves no coinciden con el formato solicitado");
        return json_encode($alerta);
        exit();
      }

      if ($clave1 != $clave2) {
        $alerta = $this->crearAlertaError("Las claves que acaba de ingresar no coinciden");
        return json_encode($alerta);
        exit();
      }

      $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
    } else {
      $clave = null;
    }

    if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {
      $foto = $insUser->guardarFoto((is_null($usuario) ? $datos['usuario'] : $usuario));

      if (is_array($foto)) {
        $alerta = $foto;
        return json_encode($alerta);
        exit();
      }
    } else {
      $foto = null;
    }

    if (is_null($usuario) && is_null($cargoId) && is_null($clave) && is_null($foto)) {
      $alerta = $this->crearAlertaRecargar("No se ha modificado ningún dato", "No ha ingresado ningún dato nuevo para ser cambiado");
      return json_encode($alerta);
      exit();
    }

    if (!is_null($usuario) || !is_null($clave) || !is_null($foto)) {
      $actualizarUser = $insUser->actualizar($id, [null, $usuario, $clave, $foto]);

      if (!$actualizarUser) {
        $alerta = $this->crearAlertaError("No se pudieron actualizar los datos del usuario, por favor intente nuevamente");
        return json_encode($alerta);
      }

      if (!is_null($foto)) $insUser->eliminarFoto($datos['foto']);
    }

    if (!is_null($cargoId)) {
      $insTrabajador = new trabajadorModel();

      $actualizarTrabajador = $insTrabajador->actualizar($datos['trabajadorID'], [null, null, null, null, null, null, null, $cargoId]);

      if (!$actualizarTrabajador) {
        $alerta = $this->crearAlertaError("No se pudo actualizar el cargo del usuario, por favor intente nuevamente");
        return json_encode($alerta);
      }
    }
    
    $alerta = $this->crearAlertaRecargar("Usuario actualizado", "Los datos del usuario han sido actualizados exitosamente");
    return json_encode($alerta);
  }

  public function obtenerDatosUsuario($id)
  {
    $id = $this->limpiarCadena($id);

    $insUser = new userModel();

    return $insUser->obtener($id);
  }

  public function obtenerTrabajadoresSinCuenta($id)
  {
    $listaTrabajadores = '';

    $consulta_trabajadores = "
      select 
        trabajadores.ID, 
        trabajadores.nombres,
        trabajadores.apellidos
      from trabajadores 
      left join cuentas on trabajadores.ID = cuentas.trabajadorID
      where (cuentas.trabajadorID is null and trabajadores.cargoID <> 5)
      or cuentas.trabajadorID = $id
      order by apellidos desc";

    $trabajadores = $this->ejecutarConsulta($consulta_trabajadores);

    if (is_null($trabajadores)) {
      return $this->mostrarError("Ha ocurrido un error al intentar cargar los cargos");
    }

    $trabajadores = $trabajadores->fetchAll();

    foreach ($trabajadores as $trabajador) {
      $listaTrabajadores .= ($trabajador['ID'] == $id)
        ? '<option value="' . $trabajador['ID'] . '" selected>' . $trabajador['nombres'] . ' ' . $trabajador['apellidos'] . '</option>'
        : '<option value="' . $trabajador['ID'] . '">' . $trabajador['nombres'] . ' ' . $trabajador['apellidos'] . '</option>';
    }

    return $listaTrabajadores;
  }

  private function errorRegistroUsuario()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del usuario");
    return json_encode($alerta);
  }
}
