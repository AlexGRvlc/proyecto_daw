<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Asegura el redireccionamiento si la sesión está iniciada
if ((isset($_SESSION['nombre']) && isset($_SESSION['id_socio']) && isset($_SESSION['rol'])) || isset($_COOKIE['nombre'])) {

    if (isset($_COOKIE['nombre'])) {
        $_SESSION['id_socio'] = $_COOKIE['id'];
        $_SESSION['nombre'] = $_COOKIE['nombre'];
        $_SESSION['rol'] = $_COOKIE['rol'];
    }

    header("Location: admin.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitgim | Log In</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div id="login-container">
        <div class="wrapper">

            <form id="form-login" action="" method="POST">
                <legend>Login</legend>


                <div class='alerta alerta_error' id="oculto-login">
                    <div class='alerta_icon'>
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div class='alerta_wrapper'>hola
                    </div>
                </div>

                <div class="input-box">
                    <input type="text" name="email" placeholder="Email Usuario">
                    <img class="form-icon" src="../public/imgs/user.svg" alt="user">
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Contraseña">
                    <img class="form-icon" src="../public/imgs/lock.svg" alt="">
                </div>

                <div class="recordar">
                    <label><input type="checkbox" name="sesion_activa" value="activo"> Recordar sesión</label>
                </div>
                <div class="centrar">
                    <button class="btn" id="btn_login">Entrar</button>
                </div>
                <div class="link-registro">
                    <a href="registro.php">Registrarse</a>
                </div>
            </form>
        </div>

    </div>

    <?php require_once "../inc/footer.inc"; ?>