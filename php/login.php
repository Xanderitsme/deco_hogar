<?php
session_start();

if(isset($_SESSION['username'])){
    header("location:../index.php");
}

if ($_SERVER["REQUEST_METHOD"] = "POST") {
    if (isset($_POST["usuario"])) {
        $_SESSION["username"] = $_POST["usuario"];
        header("location:../index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/fuentes.css">
    <link rel="stylesheet" href="../CSS/general.css">
</head>
<body>
    <main>
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" class="contenedor-inicio-sesion">
            <h1>Iniciar Sesión</h1>
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
            <label for="clave">Contraseña:</label>
            <input type="password" name="clave" id="clave" required>
            <button class="boton-amarillo">Iniciar sesión</button>
        </form>
    </main>
</body>
</html>