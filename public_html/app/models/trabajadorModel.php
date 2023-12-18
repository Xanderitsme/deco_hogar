<?php

namespace app\models;

use app\models\mainModel;
use PDO;

interface Trabajador extends Objeto
{
  const NOMBRE_TABLA = "trabajadores";
  const CAMPOS_TABLA = [
    "nombres",
    "apellidos",
    "dni",
    "telefono",
    "email",
    "fecha_contratacion",
    "sueldo",
    "cargoID"
  ];
}

class trabajadorModel extends mainModel implements Trabajador
{
  public function registrar($datos)
  {
    $datosTrabajador = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosTrabajador))
      return $this->crearAlertaError("Ocurrió un error al procesar los datos del trabajador, por favor intente más tarde");

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosTrabajador);

    if (is_null($registrar))
      return $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente más tarde");

    if ($registrar->rowCount() == 0)
      return $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente nuevamente");

    return $this->crearAlertaLimpiarSuccess("Trabajador registrado", "El trabajador " . $datos[0] . " " . $datos[1] . " ha sido registrado exitosamente");
  }

  public function obtener($id)
  {
  }

  public function actualizar($id, $datos)
  {
    $datosTrabajador = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosTrabajador))
      return false;

    $condicion = $this->empaquetarCondicion("ID", $id);

    $actualizar = $this->actualizarDatos(self::NOMBRE_TABLA, $datosTrabajador, $condicion);

    if (is_null($actualizar)) {
      return false;
    }

    if ($actualizar->rowCount() == 0) {
      return false;
    }

    if ($_SESSION['id'] == $id && !is_null($datos[7])) {
      $nombre_cargo = $this->ejecutarConsulta("select nombre_cargo from cargos where ID = " . $datos[7]);

      if (is_null($nombre_cargo)) {
        return false;
      }
  
      if ($nombre_cargo->rowCount() == 0) {
        return false;
      }

      $_SESSION['cargo'] = $nombre_cargo->fetchColumn();
      $_SESSION['cargoId'] = $datos[7];
    }

    return true;
  }

  public function eliminar($id)
  {
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
      $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar cargar los datos del trabajador");
      return $alerta;
    }

    if ($datosTrabajador->rowCount() == 0) {
      $alerta = $this->crearAlertaError("No pudimos encontrar ese trabajador en el sistema");
      return $alerta;
    }

    $datosTrabajador = $datosTrabajador->fetch(PDO::FETCH_ASSOC);

    $eliminarTrabajador = $this->eliminarRegistro(self::NOMBRE_TABLA, "ID", $id);

    if (is_null($eliminarTrabajador)) {
      $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar eliminar el usuario");
      return $alerta;
    }

    if ($eliminarTrabajador->rowCount() == 0) {
      $alerta = $this->crearAlertaError("No se pudo eliminar el usuario "  . $datosTrabajador['nombres'] . " " . $datosTrabajador['apellidos'] . ", por favor intente nuevamente");
      return $alerta;
    }

    if ($datosCuenta->rowCount() > 0) {
      $datosCuenta = $datosCuenta->fetch(PDO::FETCH_ASSOC);

      if (is_file("../views/fotos/" . $datosCuenta['foto'])) {
        chmod("../views/fotos/" . $datosCuenta['foto'], 0777);
        unlink("../views/fotos/" . $datosCuenta['foto']);
      }
    }

    $alerta = $this->crearAlertaRecargar("Trabajador eliminado", "El trabajador " . $datosTrabajador['nombres'] . " " . $datosTrabajador['apellidos'] . " se eliminó con éxito");

    return $alerta;
  }
}
