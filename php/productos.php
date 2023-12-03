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
            <section class="panel-izquierdo">
                <div class="cabecera">
                    <form class="contenedor-buscador" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                        <input type="text" class="buscador borde sombra" placeholder="Buscar productos...">
                    </form>
                </div>
                <div class="contenedor-productos borde sombra">
                    <nav>
                        <span>Nombre</span>
                        <span>Estado</span>
                        <span>Precio</span>
                        <span>Agregar</span>
                    </nav>
                    <?php for ($i = 1; $i <= 50; $i++) { ?>
                    <div class="producto">
                        <span>Refrigeradora Razer gamer RGB full fps</span>
                        <span>No disponible</span>
                        <span>S. 1799.99</span>
                        <button class="agregar borde sombra boton"><span>+</span></button>
                    </div>
                    <?php } ?>
                </div>
            </section>
            <section class="panel-derecho">
                <section class="datos-venta">
                    <span>Subtotal: 1599.98</span>
                    <button class="borde sombra boton"><span>Generar proforma</span></button>
                </section>
                <div class="productos-seleccionados borde">
                    <nav>
                        <span>Nombre</span>
                        <span>Editar</span>
                        <span class="eliminar">Eliminar</span>
                    </nav>
                    <?php for ($i = 1; $i <= 2; $i++) { ?>
                    <div class="producto">
                        <span>Refrigeradora Razer gamer RGB full fps</span>
                        <button class="agregar borde sombra boton"><span>+</span></button>
                        <button class="agregar borde sombra boton"><span>+</span></button>
                    </div>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>