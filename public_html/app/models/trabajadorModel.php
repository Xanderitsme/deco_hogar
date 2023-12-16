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

    if (is_null($datosTrabajador)) {
      $alerta = $this->crearAlertaError("Ocurrió un error al procesar los datos del trabajador, por favor intente más tarde");
      return $alerta;
    }

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosTrabajador);

    if (is_null($registrar)) {
      $alerta = $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente más tarde");
      return $alerta;
    }

    if ($registrar->rowCount() == 0) {
      $alerta = $this->crearAlertaError("No se pudo registrar el trabajador, por favor intente nuevamente");
      return $alerta;
    }

    $alerta = $this->crearAlertaLimpiarSuccess("Trabajador registrado", "El trabajador " . $datos[0] . " " . $datos[1] . " ha sido registrado exitosamente");

    return $alerta;
  }

  public function obtener($id)
  {
  }

  public function actualizar($id, $datos)
  {
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
