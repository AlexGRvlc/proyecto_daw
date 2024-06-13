<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require "config_conex.php";
    require "Database.php";
    
    $contra_ok = false;
    $email_ok = false;
    $registro_ok = false;
    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    $output =  [];

    if (isset($_POST)) {
        // Sacar las variables
        $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
        $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : null;
        $email = isset($_POST["email"]) ? $_POST["email"] : null;
        $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : null;
        $confirm_contra = isset($_POST["confirm_contrasena"]) ? $_POST["confirm_contrasena"] : null;
        $saldo = isset($_POST["saldo"]) ? $_POST["saldo"] : null;


        if ($nombre && $apellido && $email && $contrasena && $confirm_contra && $saldo) {
            if (strlen($nombre) < 1 || strlen($nombre) > 20) {
                $output = ["error" => true, "tipo_error" => "El nombre debe tener entre 1 y 20 caracteres"];
                echo json_encode($output);
                exit;
            }
            
            $expreg = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

            if (preg_match($expreg, $email)) {
                if (preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $contrasena)) {
                    $contra_ok = true;
                } else {
                    $output = ["error" => true, "tipo_error" => "La contraseña debe tener al menos 6 caracteres y contener letras y números"];
                    echo json_encode($output);
                    exit;
                }
            } else {
                $output = ["error" => true, "tipo_error" => "Email erróneo, por favor, inténtalo de nuevo"];
                echo json_encode($output);
                exit;
            }
        }else {
            $output = ["error" => true, "tipo_error" => "Ninguno de los campos obligatorios puede quedar vacío"];
            echo json_encode($output);
            exit;
        }
    }



    if($contra_ok){
        
        if(!($contrasena === $confirm_contra)){
            $output = ["error" => true, "tipo_error" => "las contraseñas no noinciden"];            
            echo json_encode($output);
            exit;
        } 
        
        $validar_email = $db->validarDatos('email', 'socios', $email);        

        if($validar_email > 0){           
            $output = ["error" => true, "tipo_error" => "Ya está registrado este email"];
            echo json_encode($output);
            exit;
        } else {            
            $email_ok = true;
        }        
    }


    if($contra_ok && $email_ok){
        $fecha = time();
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT); // Genera el hash de la contraseña
        // Se añade un socio a la BD
        $consulta = "INSERT INTO 
                            socios (nombre, apellido, email, contrasena, saldo, fecha) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $db->setConsulta($consulta);
        $db->setParam()->bind_param('ssssii', $nombre, $apellido, $email, $contrasena_hash, $saldo, $fecha);
        $db->ejecutar(); 
        $output = ["error" => false, "message" => "Registro exitoso"];
        $registro_ok = true;
    }

    $db->cerrar();
    echo json_encode($output);

    ?>
