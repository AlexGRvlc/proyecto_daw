<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../lib/config_conex.php";
require_once "../lib/date.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Semanal</title>
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
                <li><a href="admin.php">Admin</a></li>
                <li><a href="editar_socios.php">Editar</a></li>
                <li><a id="logout" href="#">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="cabecera_semanal">
        <h1>Programación de actividades</h1>
    </div>


    <div class="container_tabla">


        <?php if (isset($_GET["editar"]) && is_numeric($_GET["editar"])) : ?>
            <?php
            $db_id = $_GET["editar"];
            $db_dia = isset($_GET["nombre"]) ? $_GET["nombre"] : '';

            $db->setConsulta(
                $consulta = "SELECT
                    actividades, 
                    dia,
                    manana, 
                    media_m, 
                    ult_m,
                    tarde,
                    media_t,
                    ult_t 
                    FROM actividades"
            );
            $db->ejecutar();
            $actividades = $db->getResultado();

            $id_edit_dia = $actividades["actividades"];
            $dia_edit = $actividades["dia"];
            $manana_edit = $actividades['manana'];
            $media_m_edit = $actividades['media_m'];
            $ult_m_edit = $actividades['ult_m'];
            $tarde_edit = $actividades['tarde'];
            $media_t_edit = $actividades['media_t'];
            $ult_t_edit = $actividades['ult_t'];
            $db->despejar();
            ?>
            <div id="modal_editar">

                <div id="actividades_success">
                    <h3 class="success_msg" ></h3>
                </div>
                <div class="fail_msg">
                    <h3 id="actividades_success"></h3>
                </div>
            
                <form id="form_actividades" action="">
                    <legend>EDITAR <?php echo $db_dia; ?></legend>
                    <hr>
                    <div class="input-box-edit">
                        <input type="hidden" id="id_dia" name="id_dia" value="<?php echo $db_id; ?>">
                    </div>
                    <div class="input-box-edit">
                        <input type="hidden" id="dia" name="dia" value="<?php echo $db_dia; ?>">
                    </div>

                    <div class="input-box-edit">
                        <label for="manana">9am - 10am</label>
                        <br>
                        <select name="manana" id="manana">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="input-box-edit">
                        <label for="media_m">11am - 12am</label>
                        <br>
                        <select name="media_m" id="media_m">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="input-box-edit">
                        <label for="ult_m">13am - 14am</label>
                        <br>
                        <select name="ult_m" id="ult_m">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="input-box-edit">
                        <label for="tarde">16am - 17am</label>
                        <br>
                        <select name="tarde" id="tarde">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="input-box-edit">
                        <label for="media_t">18am - 19am</label>
                        <br>
                        <select name="media_t" id="media_t">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="input-box-edit">
                        <label for="ult_t">20am - 21am</label>
                        <br>
                        <select name="ult_t" id="ult_t">
                            <option value="default"></option>
                            <option value="yoga">yoga</option>
                            <option value="pilates">pilates</option>
                            <option value="hit">hit</option>
                            <option value="spinning">spinning</option>
                            <option value="baile">baile</option>
                            <option value="boxeo">boxeo</option>
                            <option value="funcional">funcional</option>
                        </select>
                    </div>

                    <div class="centrar">
                        <button class="btn_semanal" id="btn_semanal" type="button">Actualizar</button>
                    </div>
                </form>
            </div>

        <?php else : ?>



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
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php


                    // Datos necesarios para llenar la tabla
                    $consulta = "SELECT 
                    actividades,
                    dia,
                    manana, 
                    media_m, 
                    ult_m,
                    tarde,
                    media_t,
                    ult_t 
                    FROM actividades";
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
                <td class='table_upper' >$dia</td>
                <td>$manana</td>
                <td>$media_m</td>
                <td>$ult_m</td>                 
                <td>$tarde</td>  
                <td>$media_t</td>  
                <td>$ult_t</td>  
                <td>  
                    <button id='semanal_edit'><a href='semanal.php?editar=$id&nombre=$dia'>Editar</a></button>
                </td>        
            </tr>";
                    }
                    $db->despejar();
                    ?>


                </tbody>
            </table>




        <?php endif; ?>



    </div>



    <?php require_once "../inc/footer.inc" ?>



    <!-- 
<?php if (isset($_GET["editar"])) : ?>

<div class="editar_dia">
HOLA
</div>

<?php endif; ?> -->