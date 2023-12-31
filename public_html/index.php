<?php
require_once "./config/app.php";
require_once "./autoload.php";
require_once "./app/views/inc/session_start.php";

if (isset($_GET["views"])) {
  $url = explode("/", $_GET["views"]);
} else {
  $url = ["login"];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <?php require_once "./app/views/inc/head.php"; ?>
</head>

<body>
  <div class="contenedor-general">
    <?php

    use app\controllers\viewsController;
    use app\controllers\loginController;

    $insLogin = new loginController();

    $viewsController = new viewsController();
    $vista = $viewsController->obtenerVistasControlador($url[0]);

    if ($vista == "login" || $vista == "404") {
      require_once "./app/views/content/" . $vista . "-view.php";
    } else {
      if (!isset($_SESSION['id']) || !isset($_SESSION['trabajadorId']) || empty($_SESSION['id']) || empty($_SESSION['trabajadorId'])) {
        $insLogin->cerrarSesionControlador();
      }

      if ($insLogin->permisoAccesoVista($vista)) {
        require_once "./app/views/inc/navbar.php";
        require_once $vista;
      } else {
        require_once "./app/views/content/403-view.php";
      }
    }

    require_once "./app/views/inc/script.php";
    ?>
  </div>
</body>

</html>