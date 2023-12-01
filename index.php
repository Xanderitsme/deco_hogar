<?php
session_start();

if(!isset($_SESSION['cuentaID'])){
    header("location:php/login.php");
}

if ($_SERVER["REQUEST_METHOD"] = "POST") {
    if (isset($_POST["cerrar_sesion"])) {
        session_destroy();
        header("location:php/login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <h1>Productos</h1>
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <button name="cerrar_sesion">Cerrar sesión</button>
        </form>

        <p>Usuario:
            <?php echo $_SESSION["cuentaID"]; ?>
        </p>
    </main>
</body>
</html>