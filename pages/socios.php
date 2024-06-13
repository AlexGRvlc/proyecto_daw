<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "../lib/config_conex.php";
require_once "../lib/date.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});

if (!$_SESSION['id_socio'] && !$_SESSION['nombre'] && !$_SESSION['rol']) {
    header("Location: ../index.html");
    exit;
}

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); 

$socio_id = $_SESSION["id_socio"]; // Sacar la id del usuario para parámetro consulta

// Se requiere el nombre de usuario y su imagen
// para el panel/pantalla de registrad@
$db->setConsulta("SELECT
                id,
                CONCAT(nombre, ' ', apellido) AS nombre_completo
                FROM socios
                WHERE id = ?");

$db->setParam()->bind_param('i', $socio_id); // agregando parámetro a la consulta

$db->ejecutar();                         // ejecutando la consulta a la bd

// Obteniendo variables
$resultado = $db->getResultado();
$sesion_id = $resultado['id'];
$nombre_socio = $resultado['nombre_completo'];


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitgim | Admin</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <header id="header_admin">
        <a href="../index.html" id="logo">
            <img src="../public/imgs/logo.webp" alt="logo">
        </a>
        <span class="menu-toggle_admin" onclick="toggleMenu()">&#9776;</span>
        <nav id="nav_admin">
            <ul>
                <li><a href="actividades.php">Actividades</a></li>
                <li><a id="logout" href="#">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <div class="container_admin">
        <div class="left_admin">
            <h2>Perfil</h2>
            <p>Bienvenido <?php echo $nombre_socio; ?></p>
        </div>
    
    
    <div class="right_admin">

    <div class="container_tabla">







    <table id="tabla-actividades">
        <thead>
            <tr>
                <th></th>
                <th>9 - 10</th>
                <th>11 - 12</th>
                <th>13 - 14</th>
                <th>16 - 17</th>
                <th>18 - 19</th>
                <th>20 - 21</th>
            </tr>
        </thead>
        <tbody>
            <?php


            // Datos necesarios para paginar las salidas de socios
            // por pantalla. 
            $consulta = "SELECT 
                    actividades,
                    dia,
                    manana, 
                    media_m, 
                    ult_m,
                    tarde,
                    media_t,
                    ult_t 
                    FROM actividades 
                    ";
            $db->setConsulta($consulta);

            $db->ejecutar();


            while ($row = $db->getResultado()) {
                // Definir otras variables
                $id = $row["actividades"];
                $dia = $row["dia"];
                $manana = $row['manana'];
                $media_m = $row['media_m'];
                $ult_m = $row['ult_m'];
                $tarde = $row['tarde'];
                $media_t = $row['media_t'];
                $ult_t = $row['ult_t'];

                echo "<tr>
                <td class='table_upper'>$dia</td>
                <td>$manana <button>o</button> </td>
                <td>$media_m</td>
                <td>$ult_m</td>                 
                <td>$tarde</td>  
                <td>$media_t</td>  
                <td>$ult_t</td>        
            </tr>";
        }
        $db->despejar();
         ?>

         
        </tbody>
        </table>
</div>
    </div>
    
    
    
    
    </div>
    
    
    <script>
        function toggleMenu() {
            var menu = document.getElementById('nav_admin');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }
    </script>
            <script>
    document.getElementById('logout').addEventListener('click', function() {
        window.location.href = '../lib/logout.php';
    });
</script>
    <?php require_once "../inc/footer.php"; ?>
