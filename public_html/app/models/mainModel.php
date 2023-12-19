<?php

namespace app\models;

use PDOException;
use PDO;

if (file_exists(__DIR__ . "/../../config/server.php")) {
  require_once __DIR__ . "/../../config/server.php";
}

interface Objeto
{
  function registrar($datos);
  function obtener($id);
  function actualizar($id, $datos);
  function eliminar($id);
}

class mainModel
{

  private $server = DB_SERVER;
  private $db = DB_NAME;
  private $user = DB_USER;
  private $pass = DB_PASS;

  protected function conectar()
  {
    try {
      $conexion = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->db, $this->user, $this->pass);
      $conexion->exec("SET CHARACTER SET utf8");
      $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $conexion;
    } catch (PDOException  $ex) {
      // echo $ex->getMessage();
      echo $this->mostrarError("Ha ocurrido un problema al intentar conectarse a la base de datos, por favor intente más tarde");
      exit();
    }
  }

  protected function ejecutarConsulta($consulta)
  {
    try {
      $sql = $this->conectar()->prepare($consulta);
      $sql->execute();
    } catch (PDOException $e) {
      // echo "Error: " . $e;
      $sql = null;
    }

    return $sql;
  }

  public function limpiarCadena($cadena)
  {
    $palabras = [
      "<script>",
      "</script>",
      "<script src",
      "<script type=",
      "SELECT * FROM",
      "SELECT ",
      " SELECT ",
      "DELETE FROM",
      "INSERT INTO",
      "DROP TABLE",
      "DROP DATABASE",
      "TRUNCATE TABLE",
      "SHOW TABLES",
      "SHOW DATABASES",
      "<?php",
      "?>",
      "--",
      "^",
      "<",
      ">",
      "==",
      "=",
      ";",
      "::"
    ];

    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    foreach ($palabras as $palabra) {
      $cadena = str_ireplace($palabra, "", $cadena);
    }

    return $cadena;
  }

  protected function verificarDatos($filtro, $cadena)
  {
    if (preg_match("/^" . $filtro . "$/", $cadena)) {
      return false;
    }

    return true;
  }

  protected function guardarDatos($tabla, $datos)
  {

    $query = "insert into $tabla (";

    $contador = 0;
    foreach ($datos as $clave) {
      if ($contador >= 1) {
        $query .= ",";
      }
      $query .= $clave["campo_nombre"];
      $contador++;
    }

    $query .= ") values(";

    $contador = 0;
    foreach ($datos as $clave) {
      if ($contador >= 1) {
        $query .= ",";
      }
      $query .= $clave["campo_marcador"];
      $contador++;
    }

    $query .= ")";

    try {
      $sql = $this->conectar()->prepare($query);
      foreach ($datos as $clave) {
        $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
      }
      $sql->execute();
    } catch (PDOException) {
      $sql = null;
    }

    return $sql;
  }

  public function seleccionarDatos($tipo, $tabla, $campo, $id)
  {
    $tipo = $this->limpiarCadena($tipo);
    $tabla = $this->limpiarCadena($tabla);
    $campo = $this->limpiarCadena($campo);
    $id = $this->limpiarCadena($id);

    try {
      if ($tipo == "Unico") {
        $sql = $this->conectar()->prepare("select * from $tabla where $campo = :id");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
      } elseif ($tipo == "Normal") {
        $sql = $this->conectar()->prepare("select $campo from $tabla");
      }

      $sql->execute();
    } catch (PDOException) {
      $sql = null;
    }

    return $sql;
  }

  protected function actualizarDatos($tabla, $datos, $condicion)
  {
    $query = "update $tabla set ";

    $contador = 0;
    foreach ($datos as $clave) {
      if ($contador >= 1) {
        $query .= ",";
      }
      $query .= $clave["campo_nombre"] . " = ifnull(" . $clave["campo_marcador"] . ", " . $clave["campo_nombre"] . ")";
      $contador++;
    }

    $query .= " where " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];

    try {
      $sql = $this->conectar()->prepare($query);

      foreach ($datos as $clave) {
        $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
      }

      $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);

      $sql->execute();
    } catch (PDOException) {
      $sql = null;
    }

    return $sql;
  }

  protected function eliminarRegistro($tabla, $campo, $id)
  {
    try {
      $sql = $this->conectar()->prepare("delete from $tabla where $campo= :id");
      $sql->bindParam(":id", $id);
      $sql->execute();
    } catch (PDOException) {
      $sql = null;
    }

    return $sql;
  }

  protected function empaquetarDatos($camposTabla, $datos)
  {
    if (sizeof($camposTabla) != sizeof($datos)) return null;

    $datosEmpaquetados = array();

    $contador = 0;
    foreach ($camposTabla as $campoTabla) {
      array_push(
        $datosEmpaquetados,
        [
          "campo_nombre" => $campoTabla,
          "campo_marcador" => ":" . $campoTabla,
          "campo_valor" => $datos[$contador]
        ]
      );

      $contador++;
    }

    return $datosEmpaquetados;
  }

  protected function empaquetarCondicion($campo, $valor)
  {
    $condicionEmpaquetada = [
      "condicion_campo" => $campo,
      "condicion_marcador" => ":" . $campo,
      "condicion_valor" => $valor
    ];

    return $condicionEmpaquetada;
  }

  protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones)
  {
    $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

    if ($pagina <= 1) {
      $tabla .= '
            <a class="pagination-previous is-disabled" disabled >Anterior</a>
            <ul class="pagination-list">
            ';
    } else {
      $tabla .= '
            <a class="pagination-previous" href="' . $url . ($pagina - 1) . '/">Anterior</a>
            <ul class="pagination-list">
                <li><a class="pagination-link" href="' . $url . '1/">1</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            ';
    }


    $ci = 0;
    for ($i = $pagina; $i <= $numeroPaginas; $i++) {

      if ($ci >= $botones) {
        break;
      }

      if ($pagina == $i) {
        $tabla .= '<li><a class="pagination-link is-current" href="' . $url . $i . '/">' . $i . '</a></li>';
      } else {
        $tabla .= '<li><a class="pagination-link" href="' . $url . $i . '/">' . $i . '</a></li>';
      }

      $ci++;
    }


    if ($pagina == $numeroPaginas) {
      $tabla .= '
            </ul>
            <a class="pagination-next is-disabled" disabled >Siguiente</a>
            ';
    } else {
      $tabla .= '
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" href="' . $url . $numeroPaginas . '/">' . $numeroPaginas . '</a></li>
            </ul>
            <a class="pagination-next" href="' . $url . ($pagina + 1) . '/">Siguiente</a>
            ';
    }

    $tabla .= '</nav>';
    return $tabla;
  }

  protected function crearAlertaError($mensajeAlerta)
  {
    $alerta = [
      "tipo" => "simple",
      "titulo" => "Ocurrió un error inesperado",
      "texto" => $mensajeAlerta,
      "icono" => "error"
    ];

    return $alerta;
  }

  protected function crearAlertaLimpiarSuccess($titulo, $mensajeAlerta)
  {
    $alerta = [
      "tipo" => "limpiar",
      "titulo" => $titulo,
      "texto" => $mensajeAlerta,
      "icono" => "success"
    ];

    return $alerta;
  }

  protected function mostrarError($mensajeAlerta)
  {
    return "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrió un error inesperado',
                    text: '" . $mensajeAlerta . "',
                    confirmButtonText: 'Aceptar'
                });
            </script>
        ";
  }

  protected function crearAlertaRecargar($titulo, $mensajeAlerta)
  {
    $alerta = [
      "tipo" => "recargar",
      "titulo" => $titulo,
      "texto" => $mensajeAlerta,
      "icono" => "success"
    ];

    return $alerta;
  }
}
