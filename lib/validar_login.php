<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require "config_conex.php";
spl_autoload_register(function ($clase) {
    require_once "$clase.php";
});



$email_ok = false;
$password_ok = false;

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;

    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);



    if (!($email && $password)) {
        $output = ["error" => true, "tipo_error" => "No pueden quedar campos vacíos"];
    } else {
        $validar_email = $db->validarDatos('email', 'socios', $email);
        if ($validar_email === 0) {
            $output = ["error" => true, "tipo_error" => "No existe ese email"];
        } else {
            $db->setConsulta("SELECT id, CONCAT (nombre, ' ', apellido)  AS nombre_completo, email, contrasena, rol FROM socios WHERE email = ?");
            $db->setParam()->bind_param('s', $email);
            $db->ejecutar();
            $resultado = $db->getResultado();

            $db_id_socio = $resultado["id"];
            $db_nombre_completo = $resultado["nombre_completo"];
            $db_contrasena_hash = $resultado["contrasena"];
            $db_email = $resultado["email"];
            $db_rol = $resultado["rol"];

            if ($email === $db_email) {
                $email_ok = true;
            }
        }
    }

    if ($email_ok) {
        if (password_verify($password, $db_contrasena_hash)) {
            $password_ok = true;

            $_SESSION['id_socio'] = $db_id_socio;
            $_SESSION['nombre'] = $db_nombre_completo;
            $_SESSION['rol'] = $db_rol;

            $caduca = time() + 365 * 24 * 60 * 60;

            if (isset($_POST['sesion_activa']) && $_POST['sesion_activa'] === 'activo') {
                setcookie('id', $_SESSION['id_socio'], $caduca, "/");
                setcookie('nombre', $_SESSION['nombre'], $caduca, "/");
                setcookie('rol', $_SESSION['rol'], $caduca, "/");
            }
        } else {
            $output = ["error" => true, "tipo_error" => "Contraseña incorrecta"];
        }
    }


    if ($email_ok && $password_ok) {
        $db->cerrar();

        // para redireccionar con javaScript
        $output = ["error" => false, "rol" => $db_rol];
    }


} 
else {
    $output = ["error" => true, "tipo_error" => "Fallo al recibir el post"];
}


$json = json_encode($output);
echo $json;
