<?php
namespace app\controllers;
use app\models\mainModel;
use PDO;

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

        if (is_null($check_trabajadorId)) {
            return $this->errorRegistro();
            exit();
        }
        
        if ($check_trabajadorId->rowCount() == 0) {
            $alerta = $this->crearAlertaError("El trabajador ingresado no esta registrado");
            return json_encode($alerta);
            exit();
        }

        // Verificar si el trabajador ya tiene una cuenta registrada (opcional)

        // $check_cuenta_registrada = $this->ejecutarConsulta("select trabajadorID from cuentas where trabajadorID = $trabajadorId");

        // if (is_null($check_cuenta_registrada)) {
        //     return $this->errorRegistro();
        //     exit();
        // }
        
        // if ($check_cuenta_registrada->rowCount() > 0) {
        //     $alerta = $this->crearAlertaError("El trabajador ingresado ya tiene una cuenta registrada");
        //     return json_encode($alerta);
        //     exit();
        // }

        $check_usuario = $this->ejecutarConsulta("select usuario from cuentas where usuario = '$usuario'");

        if (is_null($check_usuario)) {
            return $this->errorRegistro();
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

    public function listarUsuarioControlador($pagina, $registros, $url, $busqueda) {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $url = $this->limpiarCadena($url);
        $url = APP_URL . $url . "/";
        $busqueda = $this->limpiarCadena($busqueda);

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1 ;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0 ;

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
                    <th class="has-text-centered" colspan="3">Opciones</th>
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

    public function eliminarUsuarioControlador() {
        $id = $this->limpiarCadena($_POST['usuario_id']);

        if ($id == 1) {
            $alerta = $this->crearAlertaError('No podemos eliminar la cuenta del usuario principal');
            return json_encode($alerta);
            exit();
        }

        $datos = $this->ejecutarConsulta("
            select
                cuentas.ID,
                cuentas.usuario,
                cuentas.foto, 
                trabajadores.nombres,
                trabajadores.apellidos,
                cargos.nombre_cargo
            from cuentas
            join trabajadores on cuentas.trabajadorID = trabajadores.ID
            join cargos on trabajadores.cargoID = cargos.ID
            where cuentas.ID <> 1
            and cuentas.ID = '$id'
        ");

        if (is_null($datos)) {
            $alerta = $this->crearAlertaError('Ha ocurrido un error al intentar cargar los datos del usuario');
            return json_encode($alerta);
            exit();
        }

        if ($datos->rowCount() <= 0) {
            $alerta = $this->crearAlertaError('No pudimos encontrar ese usuario en el sistema');
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch(PDO::FETCH_ASSOC);
        }

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

            $alerta = $this->crearAlertaRecargar("Usuario eliminado", "El usuario " . $datos['nombres'] . " " . $datos['apellidos'] . " se eliminó con éxito");
        } else {
            $alerta = $this->crearAlertaError("No se pudo eliminar el usuario "  . $datos['nombres'] . " " . $datos['apellidos'] . ", por favor intente nuevamente");
        }

        return json_encode($alerta);
    }

    private function errorRegistro() {
        $alerta = $this->crearAlertaError("Ha ocurrido un error al intentar registrar los datos del usuario");
        return json_encode($alerta);
    }
}