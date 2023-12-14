<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\productModel;
use app\models\proformaVentaModel;
use DateTime;
use PDO;

class usuarioController extends mainModel
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

    if ($this->verificarDatos("[1-9]{1,4}", $trabajadorId)) {
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
    } else {
      $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
    }

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

    $img_dir = "../views/fotos/";

    if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {
      if (!file_exists($img_dir)) {
        if (!mkdir($img_dir, 0777)) {
          $alerta = $this->crearAlertaError("Error al crear el directorio");
          return json_encode($alerta);
          exit();
        }
      }

      if (mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png") {
        $alerta = $this->crearAlertaError("La imagen que ha seleccionado es de un formato no permitido");
        return json_encode($alerta);
        exit();
      }

      if (($_FILES['usuario_foto']['size'] / 1024) > 5120) {
        $alerta = $this->crearAlertaError("La imagen que ha seleccionado supera el peso permitido");
        return json_encode($alerta);
        exit();
      }

      $foto = str_ireplace(" ", "_", $usuario);
      $foto = $foto . "_" . rand(0, 100);

      switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
        case 'image/jpeg':
          $foto = $foto . ".jpg";
          break;
        case 'image/png':
          $foto = $foto . ".png";
          break;
      }

      chmod($img_dir, 0777);

      if (!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $img_dir . $foto)) {
        $alerta = $this->crearAlertaError("No podemos subir la imagen al sistema en este momento");
        return json_encode($alerta);
        exit();
      }
    } else {
      $foto = "";
    }

    $usuario_datos_reg = [
      [
        "campo_nombre" => "trabajadorID",
        "campo_marcador" => ":trabajadorId",
        "campo_valor" => $trabajadorId
      ],
      [
        "campo_nombre" => "usuario",
        "campo_marcador" => ":usuario",
        "campo_valor" => $usuario
      ],
      [
        "campo_nombre" => "clave",
        "campo_marcador" => ":clave",
        "campo_valor" => $clave
      ],
      [
        "campo_nombre" => "foto",
        "campo_marcador" => ":foto",
        "campo_valor" => $foto
      ],
    ];

    $registrarUsuario = $this->guardarDatos("cuentas", $usuario_datos_reg);

    if (!is_null($registrarUsuario)) {
      if ($registrarUsuario->rowCount() == 1) {
        $alerta = $this->crearAlertaLimpiarSuccess("Usuario registrado", "El usuario '" . $usuario . "' ha sido registrado exitosamente");
      } else {
        if (is_file($img_dir . $foto)) {
          chmod($img_dir . $foto, 0777);
          unlink($img_dir . $foto);
        }

        $alerta = $this->crearAlertaError("No se pudo registrar el usuario, por favor intente nuevamente");
      }
    } else {
      if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
      }

      $alerta = $this->crearAlertaError("No se pudo registrar el usuario, por favor intente más tarde");
    }

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
                    <th class="has-text-centered">Nombres</th>
                    <th class="has-text-centered">Apellidos</th>
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
                    <td>' . $rows['nombres'] . '</td>
                    <td>' . $rows['apellidos'] . '</td>
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
                    <td colspan="7">
                        <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </td>
                </tr>
                ';
      } else {
        $tabla .= '
                <tr class="has-text-centered" >
                    <td colspan="7">
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

      $alerta = $this->crearAlertaRecargar("Usuario eliminado", "El usuario " . $datos['usuario'] . " (" . $datos['nombres'] . " " . $datos['apellidos'] . ") se eliminó con éxito");
    } else {
      $alerta = $this->crearAlertaError("No se pudo eliminar el usuario "  . $datos['usuario'] . " (" . $datos['nombres'] . " " . $datos['apellidos'] . "), por favor intente nuevamente");
    }

    return json_encode($alerta);
  }

  private function errorRegistroUsuario()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del usuario");
    return json_encode($alerta);
  }

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

  public function registrarProductoControlador()
  {
    $nombre = $this->limpiarCadena($_POST["producto_nombre"]);
    $stock = $this->limpiarCadena($_POST["producto_stock"]);
    $precioVenta = $this->limpiarCadena($_POST["producto_precio_venta"]);
    $precioCompra = $this->limpiarCadena($_POST["producto_precio_compra"]);
    $descripcion = $this->limpiarCadena($_POST["producto_descripcion"]);

    if (empty($nombre) || empty($descripcion) || empty($precioVenta) || empty($precioCompra) || empty($stock)) {
      $alerta = $this->crearAlertaError("No has llenado todos los campos que son obligatorios");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}", $nombre)) {
      $alerta = $this->crearAlertaError("El nombre no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}", $stock)) {
      $alerta = $this->crearAlertaError("El stock no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}\.[0-9]{2}", $precioVenta)) {
      $alerta = $this->crearAlertaError("El precio de venta no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[0-9]{1,5}\.[0-9]{2}", $precioCompra)) {
      $alerta = $this->crearAlertaError("El precio de compra no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{5,250}", $descripcion)) {
      $alerta = $this->crearAlertaError("La descripción no coincide con el formato solicitado: ");
      return json_encode($alerta);
      exit();
    }

    $check_nombre = $this->ejecutarConsulta("SELECT nombre from productos where nombre = '$nombre'");

    if (is_null($check_nombre)) {
      return $this->errorRegistroProducto();
      exit();
    }

    if ($check_nombre->rowCount() > 0) {
      $alerta = $this->crearAlertaError("El nombre del producto que acaba de ingresar ya se encuentra registrado");
      return json_encode($alerta);
      exit();
    }

    $insProducto = new productModel();

    $alerta = $insProducto->registrarProducto($nombre, $descripcion, $precioVenta, $precioCompra, $stock);

    return json_encode($alerta);
  }

  public function listarProductosVentaControlador($busqueda)
  {
    $busqueda = $this->limpiarCadena($busqueda);

    $tabla = "";

    $insProducto = new productModel();

    $datos = $insProducto->obtenerListaProductosVenta($busqueda);

    if (is_null($datos)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar los datos de los productos");
      return;
    }

    $total = $insProducto->obtenerTotalProductos($busqueda);

    if (is_null($total)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar el total de productos");
      return;
    }

    $tabla .= '
      <div class="tabla borde sombra">
        <nav class="nav-productos">
          <span>Nombre</span>
          <span>Estado</span>
          <span>Precio</span>
          <span>Agregar</span>
        </nav>
    ';

    if ($total >= 1) {
      foreach ($datos as $rows) {
        $tabla .= '
          <div class="detalles-producto">
            <span>' . $rows['nombre'] . '</span>
            <span>' . (($rows['stock'] > 0) ? 'Disponible' : 'No disponible') . '</span>
            <span>' . APP_MONEY_SYMBOL . $rows['precio_venta'] . '</span>

            <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >
              <input type="hidden" name="modulo_producto" value="agregar">
              <input type="hidden" name="producto_id" value="' . $rows['ID'] . '">
              <button type="submit" class="button is-success is-rounded is-small">Agregar</button>
            </form>
          </div>';
      }
    } else {
      $tabla .= '
          <div class="detalles-producto">
            <span>No hay registros en el sistema</span>
          </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  public function listarProductosProformaControlador($id)
  {
    $id = $this->limpiarCadena($id);

    $insProformaVenta = new proformaVentaModel();

    $datos = $insProformaVenta->obtenerProductosProformaVenta($id);

    if (is_null($datos)) {
      echo $this->mostrarError("Ha ocurrido un error al intentar cargar los datos de la proforma de venta");
      return;
    }

    $totalProforma = 0;

    foreach ($datos as $rows) {
      $totalProforma += $rows['subtotal'];
    }

    $total = sizeof($datos);

    $tabla = "";

    $tabla .= '
      <div class="tabla borde sombra">
        <nav class="nav-venta">
          <span>Total: ' . APP_MONEY_SYMBOL . $totalProforma . '</span>
          <button class="button is-info is-rounded is-normal">Generar proforma</button>
        </nav>
      </div>
      <div class="tabla borde sombra">
        <nav class="nav-prod-sel">
          <span>Nombre</span>
          <span>Opciones</span>
        </nav>
    ';

    if ($total >= 1) {
      foreach ($datos as $rows) {
        $tabla .= '
          <div class="prod-sel">
            <span>' . $rows['nombre'] . '</span>
            <button class="button is-success is-rounded is-small">Editar</button>
            <button class="button is-danger is-rounded is-small">Eliminar</button>
          </div>';
      }
    } else {
      $tabla .= '
        <div class="prod-sel">
          <span>No hay registros en el sistema</span>
        </div>';
    }

    $tabla .= '
      </div>';

    return $tabla;
  }

  private function errorRegistroProducto()
  {
    $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del producto");
    return json_encode($alerta);
  }
}
