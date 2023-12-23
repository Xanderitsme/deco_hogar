<?php

namespace app\models;

use app\models\mainModel;
use PDO;

interface Usuario extends Objeto
{
  const NOMBRE_TABLA = "cuentas";
  const CAMPOS_TABLA = [
    "trabajadorID",
    "usuario",
    "clave",
    "foto"
  ];

  const IMG_DIR = "../views/fotos/";
  const MAX_WIDTH = 250;
  const MAX_HEIGHT = 250;
}

class userModel extends mainModel implements Usuario
{
  public function registrar($datos)
  {
    $datosUsuario = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosUsuario))
      return $this->crearAlertaError("Ocurrió un error al procesar los datos del usuario, por favor intente más tarde");

    $registrar = $this->guardarDatos(self::NOMBRE_TABLA, $datosUsuario);

    if (is_null($registrar)) {
      $this->eliminarFoto($datos[3]);
      return $this->crearAlertaError("No se pudo registrar el usuario, por favor intente más tarde");
    }

    if ($registrar->rowCount() == 0) {
      $this->eliminarFoto($datos[3]);
      return $this->crearAlertaError("No se pudo registrar el usuario, por favor intente nuevamente");
    }

    return $this->crearAlertaLimpiarSuccess("Usuario registrado", "El usuario " . $datos[1] . " ha sido registrado exitosamente");
  }

  public function obtener($id)
  {
    $consulta_datos = "
      select
        cuentas.usuario,
        cuentas.foto,
        cuentas.trabajadorID,
        trabajadores.nombres,
        trabajadores.apellidos,
        trabajadores.cargoID,
        cargos.nombre_cargo
      from cuentas
      inner join trabajadores on cuentas.trabajadorID = trabajadores.ID
      inner join cargos on trabajadores.cargoID = cargos.ID
      where cuentas.ID = $id
    ";

    $datos = $this->ejecutarConsulta($consulta_datos);

    if (is_null($datos)) return null;

    if ($datos->rowCount() == 0) return null;

    return $datos->fetch(PDO::FETCH_ASSOC);
  }

  public function actualizar($id, $datos)
  {
    $datosUsuario = $this->empaquetarDatos(self::CAMPOS_TABLA, $datos);

    if (is_null($datosUsuario))
      return false;

    $condicion = $this->empaquetarCondicion("ID", $id);

    $actualizar = $this->actualizarDatos(self::NOMBRE_TABLA, $datosUsuario, $condicion);

    if (is_null($actualizar)) {
      if (!is_null($datos[3])) $this->eliminarFoto($datos[3]);
      return false;
    }

    if ($actualizar->rowCount() == 0) {
      if (!is_null($datos[3])) $this->eliminarFoto($datos[3]);
      return false;
    }

    if ($_SESSION['id'] == $id) {
      if (!is_null($datos[1])) $_SESSION['usuario'] = $datos[1];
      if (!is_null($datos[3])) $_SESSION['foto'] = $datos[3];
    }

    return true;
  }

  public function eliminar($id)
  {
  }

  public function guardarFoto($usuario)
  {
    if (!file_exists(self::IMG_DIR))
      if (!mkdir(self::IMG_DIR, 0777))
        return $this->crearAlertaError("Error al crear el directorio");

    if (mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png")
      return $this->crearAlertaError("La imagen que ha seleccionado es de un formato no permitido");

    if (($_FILES['usuario_foto']['size'] / 1024) > 5120)
      return $this->crearAlertaError("La imagen que ha seleccionado supera el peso permitido");

    $foto = str_ireplace(" ", "_", $usuario);
    $foto = $foto . "_" . rand(0, 100) . "_" . date('His');

    switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
      case 'image/jpeg':
        $foto = $foto . ".jpg";
        break;
      case 'image/png':
        $foto = $foto . ".png";
        break;
    }

    $filePath = $_FILES['usuario_foto']['tmp_name'];

    list($originalWidth, $originalHeight) = getimagesize($filePath);

    if ($originalWidth > self::MAX_WIDTH || $originalHeight > self::MAX_HEIGHT) {
      $ratio = min($originalWidth / self::MAX_WIDTH, $originalHeight / self::MAX_HEIGHT);
      $newWidth = $originalWidth / $ratio;
      $newHeight = $originalHeight / $ratio;

      $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

      switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
        case 'image/jpeg':
          $source = imagecreatefromjpeg($filePath);
          break;
        case 'image/png':
          $source = imagecreatefrompng($filePath);
          break;
      }

      imagecopyresampled($resizedImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

      $originalWidth = imagesx($resizedImage);
      $originalHeight = imagesy($resizedImage);

      $src_x = ($originalWidth - self::MAX_WIDTH) / 2;
      $src_y = ($originalHeight - self::MAX_HEIGHT) / 2;

      $dst_image = imagecreatetruecolor(self::MAX_WIDTH, self::MAX_HEIGHT);

      imagecopyresampled($dst_image, $resizedImage, 0, 0, $src_x, $src_y, self::MAX_WIDTH, self::MAX_HEIGHT, self::MAX_WIDTH, self::MAX_HEIGHT);

      switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
        case 'image/jpeg':
          imagejpeg($dst_image, self::IMG_DIR . $foto, 100);
          break;
        case 'image/png':
          imagepng($dst_image, self::IMG_DIR . $foto, 10);
          break;
      }

      imagedestroy($resizedImage);
      imagedestroy($source);
      imagedestroy($dst_image);
    } else {
      move_uploaded_file($_FILES['usuario_foto']['tmp_name'], self::IMG_DIR . $foto);
    }

    return $foto;
  }

  public function eliminarFoto($foto)
  {
    if (is_file(self::IMG_DIR . $foto)) {
      chmod(self::IMG_DIR . $foto, 0777);
      unlink(self::IMG_DIR . $foto);
    }
  }
}
