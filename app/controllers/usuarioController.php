<?php
namespace app\controllers;
use app\models\mainModel;

class usuarioController extends mainModel {

    public function registrarUsuarioControlador() {

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

        if (!is_null($check_trabajadorId)) {
            if ($check_trabajadorId->rowCount() == 0) {
                $alerta = $this->crearAlertaError("El trabajador ingresado no esta registrado");
                return json_encode($alerta);
                exit();
            }
        } else {
            return $this->errorRegistro();
            exit();
        }

        // Verificar si el trabajador ya tiene una cuenta registrada (opcional)

        // $check_cuenta_registrada = $this->ejecutarConsulta("select trabajadorID from cuentas where trabajadorID = $trabajadorId");

        // if (!is_null($check_cuenta_registrada)) {
        //     if ($check_cuenta_registrada->rowCount() > 0) {
        //         $alerta = $this->crearAlertaError("El trabajador ingresado ya tiene una cuenta registrada");
        //         return json_encode($alerta);
        //         exit();
        //     }
        // } else {
        //     return $this->errorRegistro();
        //     exit();
        // }

        $check_usuario = $this->ejecutarConsulta("select usuario from cuentas where usuario like '$usuario'");
        if (!is_null($check_usuario)) {
            if ($check_usuario->rowCount() > 0) {
                $alerta = $this->crearAlertaError("El nombre de usuario ingresado ya se encuentra registrado");
                return json_encode($alerta);
                exit();
            }
        } else {
            return $this->errorRegistro();
            exit();
        }

        $img_dir = "../views/fotos/";

        if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {
            if (!file_exists($img_dir)) {
                if (!mkdir($img_dir,0777)) {
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
            $foto = $foto . "_" . rand(0,100);

            switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
                case 'image/jpeg':
                    $foto = $foto . ".jpg";
                    break;
                case 'image/png':
                    $foto = $foto . ".png";
                    break;
            }

            chmod($img_dir,0777);

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
                "campo_nombre" => "trabajadorId",
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

    private function errorRegistro() {
        $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del usuario");
        return json_encode($alerta);
    }
}