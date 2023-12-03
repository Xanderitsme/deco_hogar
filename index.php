<?php
session_start();

if(isset($_SESSION['cuentaID'])){
    header("location:php/productos.php");
}

if ($_SERVER["REQUEST_METHOD"] = "POST") {
    if (isset($_POST["usuario"])) {
        $_SESSION["cuentaID"] = $_POST["usuario"];
        header("location:php/productos.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body>
    <main>
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" class="contenedor-inicio-sesion">
            <h1>Iniciar Sesión</h1>
            <label for="usuario">Nombre de usuario</label>
            <input class="borde sombra" type="text" name="usuario" required>
            <label for="clave">Contraseña</label>
            <input class="borde sombra" type="password" name="clave" required>
            <button class="boton borde sombra">Iniciar sesión</button>
        </form>
    </main>
</body>
</html>