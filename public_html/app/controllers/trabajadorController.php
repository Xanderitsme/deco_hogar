<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\trabajadorModel;
use DateTime;
use PDO;

class trabajadorController extends mainModel
{
  public function registrarTrabajadorControlador()
  {
    $nombres = $this->limpiarCadena($_POST["trabajador_nombres"]);
    $apellidos = $this->limpiarCadena($_POST["trabajador_apellidos"]);
    $dni = $this->limpiarCadena($_POST["trabajador_dni"]);
    $telefono = $this->limpiarCadena($_POST["trabajador_telefono"]);
    $email = $this->limpiarCadena($_POST["trabajador_email"]);
    $fechaTemp = $this->limpiarCadena($_POST["trabajador_fecha_contratacion"]);
    $fechaContratacion = DateTime::createFromFormat('Y-m-d', $fechaTemp)->format('Y-m-d');
    $sueldo = $this->limpiarCadena($_POST["trabajador_sueldo"]);
    $cargoId = $this->limpiarCadena($_POST["trabajador_cargo"]);

    if (empty($nombres) || empty($apellidos) || empty($dni) || empty($fechaContratacion) || empty($sueldo) || empty($cargoId)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombres)) {
      $alerta = $this->crearAlertaError("El nombre no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellidos)) {
      $alerta = $this->crearAlertaError("El apellido no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{8}", $dni)) {
      $alerta = $this->crearAlertaError("El DNI no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{4}-[0-9]{2}-[0-9]{2}", $fechaContratacion)) {
      $alerta = $this->crearAlertaError("La fecha de contratación no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{3,5}", $sueldo)) {
      $alerta = $this->crearAlertaError("El sueldo no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[1-9]{1,3}", $cargoId)) {
      $alerta = $this->crearAlertaError("El cargo no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    }

    $check_cargo = $this->ejecutarConsulta("select ID from cargos where ID = '$cargoId'");

    if (is_null($check_cargo)) {
      return $this->errorRegistroTrabajador();
      exit();
    }

    if ($check_cargo->rowCount() == 0) {
      $alerta = $this->crearAlertaError("El cargo ingresado no existe");
      return json_encode($alerta);
      exit();
    }

    if (empty($email)) {
      $email = null;
    } else {
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alerta = $this->crearAlertaError("Ha ingresado un email no válido");
        return json_encode($alerta);
        exit();
      }

      $check_email = $this->ejecutarConsulta("select email from trabajadores where email = '$email'");

      if (is_null($check_email)) {
        return $this->errorRegistroTrabajador();
        exit();
      }

      if ($check_email->rowCount() > 0) {
        $alerta = $this->crearAlertaError("El email que acaba de ingresar ya se encuentra registrado");
        return json_encode($alerta);
        exit();
      }
    }

    if (empty($telefono)) {
      $telefono = null;
    } else {
      if ($this->verificarDatos("[0-9]{9}", $telefono)) {
        $alerta = $this->crearAlertaError("Ha ingresado un numero de celular no no válido");
        return json_encode($alerta);
        exit();
      }

      $check_telefono = $this->ejecutarConsulta("select telefono from trabajadores where telefono = '$telefono'");

      if (is_null($check_telefono)) {
        return $this->errorRegistroTrabajador();
        exit();
      }

      if ($check_telefono->rowCount() > 0) {
        $alerta = $this->crearAlertaError("El numero de celular que acaba de ingresar ya se encuentra registrado");
        return json_encode($alerta);
        exit();
      }
    }

    $insTrabajador = new trabajadorModel();

    $alerta = $insTrabajador->registrar([$nombres, $apellidos, $dni, $telefono, $email, $fechaContratacion, $sueldo, $cargoId]);

    return json_encode($alerta);
  }

  public function listarTrabajadoresControlador($pagina, $registros, $url, $busqueda)
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
            trabajadores.ID,
            trabajadores.nombres,
            trabajadores.apellidos,
            trabajadores.dni,
            trabajadores.telefono,
            trabajadores.email,
            trabajadores.fecha_contratacion,
            trabajadores.sueldo,
            cargos.nombre_cargo
        from trabajadores 
        join cargos on trabajadores.cargoID = cargos.ID
        where trabajadores.ID <> " . $_SESSION['id'] . "
        and trabajadores.ID <> 1
        and (
            trabajadores.nombres like '%" . $busqueda . "%'
            or trabajadores.apellidos like '%" . $busqueda . "%'
            or cargos.nombre_cargo like '%" . $busqueda . "%'
            or trabajadores.dni like '%" . $busqueda . "%'
            or trabajadores.telefono like '%" . $busqueda . "%'
            or trabajadores.email like '%" . $busqueda . "%'
        )
        order by apellidos asc limit " . $inicio . ", " . $registros;

      $consulta_total = "
        select count(*) as total from trabajadores 
        where trabajadores.ID <> " . $_SESSION['id'] . "
        and trabajadores.ID <> 1 
        and (
            trabajadores.nombres like '%" . $busqueda . "%'
            or trabajadores.apellidos like '%" . $busqueda . "%'
            or cargos.nombre_cargo like '%" . $busqueda . "%'
            or trabajadores.dni like '%" . $busqueda . "%'
            or trabajadores.telefono like '%" . $busqueda . "%'
            or trabajadores.email like '%" . $busqueda . "%'
        )";
    } else {
      $consulta_datos = "
        select
            trabajadores.ID,
            trabajadores.nombres,
            trabajadores.apellidos,
            trabajadores.dni,
            trabajadores.telefono,
            trabajadores.email,
            trabajadores.fecha_contratacion,
            trabajadores.sueldo,
            cargos.nombre_cargo
        from trabajadores 
        join cargos on trabajadores.cargoID = cargos.ID
        where trabajadores.ID <> " . $_SESSION['id'] . "
        and trabajadores.ID <> 1
        order by apellidos asc limit " . $inicio . ", " . $registros;

      $consulta_total = "
        select count(*) as total from trabajadores 
        where trabajadores.ID <> " . $_SESSION['id'] . "
        and trabajadores.ID <> 1";
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
              <th class="has-text-centered">Nombres y apellidos</th>
              
              <th class="has-text-centered">DNI</th>
              <th class="has-text-centered">Teléfono</th>
              <th class="has-text-centered">Correo electrónico</th>
              <th class="has-text-centered">Cargo</th>
              <th class="has-text-centered">Fecha de contratación</th>
              <th class="has-text-centered">Sueldo</th>
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
                    <td>' . $rows['nombres'] . ' ' . $rows['apellidos'] . '</td>
                    <td>' . $rows['dni'] . '</td>
                    <td>' . $rows['telefono'] . '</td>
                    <td>' . $rows['email'] . '</td>
                    <td>' . $rows['nombre_cargo'] . '</td>
                    <td>' . date("d-m-Y", strtotime($rows['fecha_contratacion'])) . '</td>
                    <td>' . $rows['sueldo'] . '</td>
                    <td>
                        <a href="' . APP_URL . 'actualizarTrabajador/' . $rows['ID'] . '/" class="button is-success is-rounded is-small">Actualizar</a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/trabajadorAjax.php" method="POST" autocomplete="off" >

                            <input type="hidden" name="modulo_trabajador" value="eliminar">
                            <input type="hidden" name="trabajador_id" value="' . $rows['ID'] . '">

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
                    <td colspan="10">
                        <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </td>
                </tr>
                ';
      } else {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td colspan="10">
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
                Mostrando trabajadores <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> 
                de un <strong>total de ' . $total . '</strong>
            </p>
            ';

      $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 10);
    }

    return $tabla;
  }

  public function eliminarTrabajadorControlador()
  {
    $id = $this->limpiarCadena($_POST['trabajador_id']);

    if ($id == 1) {
      $alerta = $this->crearAlertaError("No podemos eliminar la cuenta del usuario principal");
      return json_encode($alerta);
      exit();
    }

    $insTrabajador = new trabajadorModel();

    $alerta = $insTrabajador->eliminar($id);

    return json_encode($alerta);
  }

  public function obtenerCargos()
  {
    $listaCargos = '';

    $consulta_cargos = "select ID, nombre_cargo from cargos order by nombre_cargo desc";

    $cargos = $this->ejecutarConsulta($consulta_cargos);

    if (is_null($cargos)) {
      return $this->mostrarError("Ha ocurrido un error al intentar cargar los cargos");
    }

    $cargos = $cargos->fetchAll();

    foreach ($cargos as $cargo) {
      $listaCargos .= '<option value="' . $cargo['ID'] . '">' . $cargo['nombre_cargo'] . '</option>';
    }

    return $listaCargos;
  }

  private function errorRegistroTrabajador()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del trabajador");
    return json_encode($alerta);
  }
}
