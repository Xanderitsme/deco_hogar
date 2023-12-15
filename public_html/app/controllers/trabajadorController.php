<?php

namespace app\controllers;

use app\models\mainModel;
use DateTime;
use PDO;


class trabajadorController extends mainModel
{
  public function registrarTrabajadorControlador()
  {
    $nombres = $this->limpiarCadena($_POST["trabajador_nombres"]);
    $apellidos = $this->limpiarCadena($_POST["trabajador_apellidos"]);
    $dni = $this->limpiarCadena($_POST["trabajador_dni"]);
    $email = $this->limpiarCadena($_POST["trabajador_email"]);
    $telefono = $this->limpiarCadena($_POST["trabajador_telefono"]);
    $fechaTemp = $this->limpiarCadena($_POST["trabajador_fecha_contratacion"]);
    $fechaContratacion = DateTime::createFromFormat('Y-m-d', $fechaTemp)->format('Y-m-d');
    $sueldo = $this->limpiarCadena($_POST["trabajador_sueldo"]);
    $cargo = $this->limpiarCadena($_POST["trabajador_cargo"]);
    $cargoId = "";

    if (empty($nombres) || empty($apellidos) || empty($dni) || empty($fechaContratacion) || empty($sueldo) || empty($cargo)) {
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

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $cargo)) {
      $alerta = $this->crearAlertaError("El cargo no coincide con el formato solicitado");
      return json_encode($alerta);
      exit();
    } else {
      $check_cargo = $this->ejecutarConsulta("select ID from cargos where nombre_cargo = '$cargo'");

      if (!is_null($check_cargo)) {
        if ($check_cargo->rowCount() > 0) {
          $resultado = $check_cargo->fetch(PDO::FETCH_ASSOC);
          $cargoId = $resultado["ID"];
        } else {
          $alerta = $this->crearAlertaError("El cargo ingresado no existe");
          return json_encode($alerta);
          exit();
        }
      } else {
        return $this->errorRegistroTrabajador();
        exit();
      }
    }

    if (!empty($email)) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check_email = $this->ejecutarConsulta("select email from trabajadores where email = '$email'");

        if (!is_null($check_email)) {
          if ($check_email->rowCount() > 0) {
            $alerta = $this->crearAlertaError("El email que acaba de ingresar ya se encuentra registrado");
            return json_encode($alerta);
            exit();
          }
        } else {
          return $this->errorRegistroTrabajador();
          exit();
        }
      } else {
        $alerta = $this->crearAlertaError("Ha ingresado un email no válido");
        return json_encode($alerta);
        exit();
      }
    } else {
      $email = null;
    }

    if (!empty($telefono)) {
      if ($this->verificarDatos("[0-9]{9}", $telefono)) {
        $alerta = $this->crearAlertaError("Ha ingresado un numero de celular no no válido");
        return json_encode($alerta);
        exit();
      } else {
        $check_telefono = $this->ejecutarConsulta("select telefono from trabajadores where telefono = '$telefono'");

        if (!is_null($check_telefono)) {
          if ($check_telefono->rowCount() > 0) {
            $alerta = $this->crearAlertaError("El numero de celular que acaba de ingresar ya se encuentra registrado");
            return json_encode($alerta);
            exit();
          }
        } else {
          return $this->errorRegistroTrabajador();
          exit();
        }
      }
    } else {
      $telefono = null;
    }

    $trabajador_datos_reg = [
      [
        "campo_nombre" => "nombres",
        "campo_marcador" => ":nombres",
        "campo_valor" => $nombres
      ],
      [
        "campo_nombre" => "apellidos",
        "campo_marcador" => ":apellidos",
        "campo_valor" => $apellidos
      ],
      [
        "campo_nombre" => "dni",
        "campo_marcador" => ":dni",
        "campo_valor" => $dni
      ],
      [
        "campo_nombre" => "telefono",
        "campo_marcador" => ":telefono",
        "campo_valor" => $telefono
      ],
      [
        "campo_nombre" => "email",
        "campo_marcador" => ":email",
        "campo_valor" => $email
      ],
      [
        "campo_nombre" => "fecha_contratacion",
        "campo_marcador" => ":fechaContratacion",
        "campo_valor" => $fechaContratacion
      ],
      [
        "campo_nombre" => "sueldo",
        "campo_marcador" => ":sueldo",
        "campo_valor" => $sueldo
      ],
      [
        "campo_nombre" => "cargoID",
        "campo_marcador" => ":cargoId",
        "campo_valor" => $cargoId
      ],
    ];

    $registrarTrabajador = $this->guardarDatos("trabajadores", $trabajador_datos_reg);

    if (!is_null($registrarTrabajador)) {
      if ($registrarTrabajador->rowCount() == 1) {
        $alerta = $this->crearAlertaLimpiarSuccess("Trabajador registrado", "El trabajador " . $nombres . " " . $apellidos . " ha sido registrado exitosamente");
      } else {
        $alerta = $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente nuevamente");
      }
    } else {
      $alerta = $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente más tarde");
    }

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
              <th class="has-text-centered">Nombres</th>
              <th class="has-text-centered">Apellidos</th>
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
                    <td>' . $rows['nombres'] . '</td>
                    <td>' . $rows['apellidos'] . '</td>
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
                        <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

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
                    <td colspan="11">
                        <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </td>
                </tr>
                ';
      } else {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td colspan="11">
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
      $alerta = $this->crearAlertaError('No podemos eliminar la cuenta del usuario principal');
      return json_encode($alerta);
      exit();
    }

    $datosTrabajador = $this->ejecutarConsulta("
      select 
        trabajadores.nombres,
        trabajadores.apellidos 
      from trabajadores 
      where trabajadores.ID <> 1 
      and trabajadores.ID = $id
    ");

    $datosCuenta = $this->ejecutarConsulta("
      select 
        cuentas.foto
      from cuentas 
      where cuentas.ID <> 1 
      and cuentas.trabajadorID = $id
    ");

    if (is_null($datosTrabajador) || is_null($datosCuenta)) {
      $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar cargar los datos del trabajador');
      return json_encode($alerta);
      exit();
    }

    if ($datosTrabajador->rowCount() == 0) {
      $alerta = $this->crearAlertaError('No pudimos encontrar ese trabajador en el sistema');
      return json_encode($alerta);
      exit();
    }

    $datosTrabajador = $datosTrabajador->fetch(PDO::FETCH_ASSOC);

    $eliminarTrabajador = $this->eliminarRegistro("trabajadores", "ID", $id);

    if (is_null($eliminarTrabajador)) {
      $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar eliminar el usuario');
      return json_encode($alerta);
      exit();
    }

    if ($eliminarTrabajador->rowCount() == 1) {
      if ($datosCuenta->rowCount() > 0) {
        $datosCuenta = $datosCuenta->fetch(PDO::FETCH_ASSOC);
        if (is_file("../views/fotos/" . $datosCuenta['foto'])) {
          chmod("../views/fotos/" . $datosCuenta['foto'], 0777);
          unlink("../views/fotos/" . $datosCuenta['foto']);
        }
      }

      $alerta = $this->crearAlertaRecargar("Trabajador eliminado", "El trabajador " . $datosTrabajador['nombres'] . " " . $datosTrabajador['apellidos'] . " se eliminó con éxito");
    } else {
      $alerta = $this->crearAlertaError("No se pudo eliminar el usuario "  . $datosTrabajador['nombres'] . " " . $datosTrabajador['apellidos'] . ", por favor intente nuevamente");
    }

    return json_encode($alerta);
  }

  private function errorRegistroTrabajador()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del trabajador");
    return json_encode($alerta);
  }
}
