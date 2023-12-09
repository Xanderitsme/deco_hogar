<?php
namespace app\controllers;
use app\models\mainModel;
use DateTime;
use PDO;

class trabajadorController extends mainModel {

    public function registrarTrabajadorControlador() {
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
                return $this->errorRegistro();
                exit();
            }
        }

        if (!empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("select correo_electronico from trabajadores where correo_electronico = '$email'");

                if (!is_null($check_email)) {
                    if ($check_email->rowCount() > 0) {
                        $alerta = $this->crearAlertaError("El email que acaba de ingresar ya se encuentra registrado");
                        return json_encode($alerta);
                        exit();
                    }
                } else {
                    return $this->errorRegistro();
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
                    return $this->errorRegistro();
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

    private function errorRegistro() {
        $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del trabajador");
        return json_encode($alerta);
    }
}