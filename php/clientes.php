<?php
session_start();

if(!isset($_SESSION['cuentaID'])){
    header("location:../index.php");
}

if ($_SERVER["REQUEST_METHOD"] = "POST") {
    if (isset($_POST["cerrar_sesion"])) {
        session_destroy();
        header("location:../index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/general.css">
    <link rel="stylesheet" href="../CSS/productos.css">
</head>
<body>
    <section class="contenedor-general">
        <?php include("header.php") ?>
        <?php include("navbar.php") ?>

        <main class="contenido">
            AAAAAAA
        </main>
    </div>
</body>
</html>