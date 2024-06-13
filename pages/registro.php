<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitgim | Log In</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div id="registro-container">
        <div class="wrapper">
            <div id="registro_success">
                <h2>¡Te has registrado con éxito!</h2>
            </div>

            <div id="registro_fail">
                <p id="msg_error"></p>
            </div>


            <form id="form_registro">
                <legend>Registro</legend>

                <div class="input-box">
                    <input type="text" name="nombre" placeholder="Nombre">
                </div>

                <div class="input-box">
                    <input type="text" name="apellido" placeholder="Apellido">
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email">
                </div>

                <div class="input-box">
                    <input type="text" name="contrasena" placeholder="Contraseña">
                </div>

                <div class="input-box">
                    <input type="text" name="confirm_contrasena" placeholder="Confirmar Contraseña">
                </div>

                <div class="input-box">
                    <input type="number" name="saldo" min="50" placeholder="Saldo">
                </div>

                <div class="centrar">
                    <button class="btn" id="btn_registro">Entrar</button>
                </div>
                <div class="link-registro">
                    <a href="login.php">Ya estoy registrado</a>                    
                </div>
            </form>
        </div>



    </div>

    <?php require_once "../inc/footer.inc"; ?>
