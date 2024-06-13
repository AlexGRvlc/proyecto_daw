<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../lib/config_conex.php";
require_once "../lib/date.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});

if (!$_SESSION['id_socio'] && !$_SESSION['nombre'] && !$_SESSION['rol']) {
    header("Location: ../index.html");
    exit;
}

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); // instanciando clase mysqli

$socio_id = $_SESSION["id_socio"]; // Sacar la id del usuario para parámetro consulta

// panel de admin - perfil
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
// Liberar la info almacenada en la llamada a BD
$db->despejar();

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
                <li><a href="admin.php">Admin</a></li>
                <li><a href="semanal.php">Semanal</a></li>
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

            <div id="cabecera_admin">
                <h2>Edición de Socios</h2>
                <div id="fecha">
                    <i class="bi bi-calendar3"></i>
                    <span><?php echo "$dia $dia_date $mes, $anyo"; ?></span>
                </div>
            </div>


            <?php if (isset($_GET["editar"])) : ?>

                <?php

                $db_id = $_GET["editar"];

                $db->setConsulta(
                    "SELECT                
                nombre,
                apellido,
                email, 
                saldo
                FROM socios
                WHERE id = $db_id"
                );
                $db->ejecutar();
                $result = $db->getResultado();
                $nombre_edit = $result["nombre"];
                $apellido_edit = $result["apellido"];
                $email_edit = $result["email"];
                $saldo_edit = $result["saldo"];

                ?>

                <div id="modal_editar">
                    <div class="edit_success">
                        <h4>Socio actualizado con éxito</h4>
                    </div>
                    <form id="form_registro" action="../lib/actualizar_socio.php" method="POST">
                        <legend>EDITAR</legend>

                        <div class="input-box-edit">
                            <input type="hidden" id="edit_id" name="id" value="<?php echo $db_id; ?>">
                        </div>

                        <div class="input-box-edit">
                            <input type="text" id="edit_name" name="nombre" value="<?php echo $nombre_edit; ?>">
                        </div>

                        <div class="input-box-edit">
                            <input type="text" name="apellido" value="<?php echo $apellido_edit; ?>">
                        </div>

                        <div class="input-box-edit">
                            <input type="email" name="email" value="<?php echo $email_edit; ?>">
                        </div>

                        <div class="input-box-edit">
                            <input type="number" name="saldo" min="50" value="<?php echo $saldo_edit; ?>">
                        </div>

                        <div class="centrar">
                            <button class="btn_actualizar" id="btn_actualizar" name="actualizar" type="submit">Actualizar</button>
                        </div>
                    </form>
                </div>


            <?php elseif (isset($_GET["confirm_eliminar"])) : ?>
                <?php echo var_dump($_GET); ?>

                <div class="eliminar_socio">
                    <h2>Seguro?</h2>
                    <a href='<?php echo "../lib/eliminar.php"; ?>'>Si</a>
                    <a href='editar_socios.php'>No</a>

                </div>



            <?php else : ?>

                <?php
                // Para sacar variables a mostrar en el panel 
                // la info de los socios a editar/eliminar
                $db->setConsulta("SELECT 
                                        id,
                                        CONCAT (nombre, ' ', apellido)  AS nombre_completo, 
                                        email, 
                                        saldo, 
                                        fecha 
                                        FROM socios 
                                        ORDER BY fecha DESC LIMIT 10 ");
                $db->ejecutar();

                ?>



                <div class="table-responsive">
                    <div class="table-cabecera">
                        <h3>Actualiza o elimina a algún socio</h3>
                        <div class="search-container">
                        <form action="" id="busqueda" method="GET">
                            <input type="text" name="busqueda" placeholder="Buscar..." class="search-box">
                            <button type="submit" class="search-button">Buscar</button>
            </form>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class='sm-hide'>Email</th>
                                <th class='sm-hide'>Saldo</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // consulta para contar el número total de socios
                            // para la paginación
                            $db->setConsulta("SELECT
                                            COUNT(id) AS total_socios
                                            FROM socios");
                            $db->ejecutar();
                            $resultado = $db->getResultado();
                            $total_socios = $resultado["total_socios"];
                            $db->despejar();


                            $porPagina = 3;           // socios_x_pagina 
                            $pagina = (isset($_GET["pagina"])) ? (int)$_GET['pagina'] : 1;     // página actual
                            $inicio = ($pagina - 1) * $porPagina; // indice de inicio xra consulta paginada



                            if (isset($_GET["busqueda"])) {

                                if (empty($_GET["busqueda"])) {
                                    echo "<h5>No has aplicado ningún patrón de búsqueda</h5>";
                                    exit;
                                }

                                // Consulta para mostrar la info del socio/s buscado/s
                                $consulta = "SELECT 
                                            id,
                                            CONCAT(nombre, ' ', apellido) AS nombre_completo, 
                                            email, 
                                            saldo, 
                                            fecha 
                                            FROM socios 
                                            WHERE 1=1";

                                $busqueda = trim($_GET["busqueda"]); // Eliminar espacios en blanco


                                $busqueda_nombres = explode(" ", $busqueda);

                                $condiciones = [];
                                foreach ($busqueda_nombres as $nombre) {
                                    $condiciones[] = "(nombre LIKE '%" . $nombre . "%' OR apellido LIKE '%" . $nombre . "%')";
                                }

                                if (count($condiciones) > 0) {
                                    $consulta .= " AND (" . implode(" OR ", $condiciones) . ")";
                                }

                                $consulta .= " ORDER BY fecha LIMIT $inicio, $porPagina";
                                $db->setConsulta($consulta);

                                // paginación búsqueda
                                $consulta_busqueda = "SELECT 
                                                                      id,
                                                                      CONCAT(nombre, ' ', apellido) AS nombre_completo, 
                                                                      email, 
                                                                      saldo, 
                                                                      fecha 
                                                                  FROM socios 
                                                                  WHERE ";




                                $condiciones = [];
                                foreach ($busqueda_nombres as $nombre) {
                                    $condiciones[] = "(nombre LIKE '%" . $nombre . "%' OR apellido LIKE '%" . $nombre . "%')";
                                }

                                if (count($condiciones) > 0) {
                                    $consulta_busqueda .= implode(" OR ", $condiciones);
                                } else {
                                    // Si no hay condiciones, seleccionar todos los socios
                                    $consulta_busqueda .= "1";
                                }

                                // Consulta de conteo
                                $consulta_contador = "SELECT COUNT(id) AS contador FROM socios WHERE ";

                                if (count($condiciones) > 0) {
                                    $consulta_contador .= implode(" OR ", $condiciones);
                                } else {
                                    // Si no hay condiciones, contar todos los socios
                                    $consulta_contador .= "1";
                                }

                                // Ejecutar la consulta de conteo
                                $db->setConsulta($consulta_contador);
                                $db->ejecutar();
                                $resultado_contador = $db->getResultado();

                                // Obtener el valor del conteo
                                $contador = intval($resultado_contador["contador"]); //-------------HERE---------------------------
                                $consulta_busqueda .= " ORDER BY fecha LIMIT $inicio, $porPagina";

                                $paginas = ceil($contador / $porPagina);             // nº total páginas
                                // Ejecutar la consulta para obtener los resultados de búsqueda
                                $db->setConsulta($consulta_busqueda);
                                $db->ejecutar();

                                // Mostrar los resultados
                                while ($fila = $db->getResultado()) {
                                    // Procesar y mostrar cada fila de resultado aquí
                                    // Ejemplo: echo $fila["id_socio"], $fila["nombre_completo"], etc.
                                }

                                if ($contador > 1 || $contador == 0) {
                                    echo "<h4>$contador resultados encontrados</h4>";
                                } else {
                                    echo "<h4>$contador resultado encontrado</h4>";
                                }
                            } else {

                                $paginas = ceil($total_socios / $porPagina);             // nº total páginas

                                // Datos necesarios para paginar las salidas de socios
                                // por pantalla. 
                                $consulta = "SELECT 
                                            id,
                                            CONCAT (nombre, ' ', apellido)  AS nombre_completo, 
                                            email, 
                                            saldo, 
                                            fecha 
                                            FROM socios 
                                            ORDER BY fecha
                                            LIMIT $inicio, $porPagina";
                                $db->setConsulta($consulta);
                            }



                            $db->ejecutar();
                            $contador = $inicio;

                            // Creación de la tabla con los resultados de BD
                            while ($row = $db->getResultado()) {
                                $contador++;


                                // Separar el nombre y el apellido
                                $nombre_completo = $row['nombre_completo'];
                                $nombres = explode(' ', $nombre_completo);
                                $nombre = $nombres[0]; // Primer nombre
                                $apellido = end($nombres); // Último nombre (apellido)

                                // Definir otras variables
                                $id_socio = $row['id'];
                                $email = $row['email'];
                                $saldo = $row['saldo'];
                                $fechaFormateada = date('d - m - Y', $row['fecha']);
                                $db_id = $row["id"];
                                echo "<tr>
                                    <td>$contador</td>
                                    <td>{$row['nombre_completo']}</td>
                                    <td class='sm-hide' >{$row['email']}</td>
                                    <td class='sm-hide' >{$row['saldo']}</td>                 
                                    <td  >$fechaFormateada</td>  
                                    <td>  
                                        <a href='editar_socios.php?editar=$db_id' 
                                        class='btn btn-success acciones accion_editar'
                                        data-toggle='tooltip' title='Editar'
                                        data-id='{$id_socio}' 
                                        data-nombre='{$nombre}' 
                                        data-apellido='{$apellido}' 
                                        data-email='{$email}' 
                                        data-saldo='{$saldo}'
                                        data-bs-toggle='modal'
                                        data-bs-target='#modal_editar' 
                                        title='Editar'>
                                        Actualizar
                                        </a>
                                        <a href='editar_socios.php?confirm_eliminar' 
                                        class='btn btn-danger acciones accion_eliminar' 
                                        data-toggle='tooltip' 
                                        data-id='{$id_socio}'
                                        title='Eliminar'>
                                        Eliminar
                                        </a>
                                    </td>        
                                </tr>";
                            }
                            $db->despejar();
                            ?>
                        </tbody>
                    </table>


                    <div id="caja_eliminar" class="modal-wrapper-css" id="modal">
                        <div class="modal-content-css">
                            <h2>¿Seguro?</h2>
                            <p>Este es el contenido de la ventana modal. Puedes poner lo que quieras aquí.</p>
                            <button id="si">Sí</button>
                            <button id="no">No</button>
                        </div>
                    </div>

                    <?php

                    $anterior = ($pagina - 1);
                    $siguiente = ($pagina + 1);

                    // variables para la paginación de la búsqueda/normal
                    if (isset($_GET["busqueda"])) {
                        $pag_anterior = "?pagina=$anterior&busqueda={$_GET['busqueda']}";
                        $pag_siguiente = "?pagina=$siguiente&busqueda={$_GET['busqueda']}";
                    } else {
                        $pag_anterior = "?pagina=$anterior";
                        $pag_siguiente = "?pagina=$siguiente";
                    }


                    ?>

                    <nav aria-label="nav">
                        <ul class="pagination justify-content-center"> <!-- Centra los elementos de la paginación -->

                            <!-- 
                                        Opciones para mostrar o no los iconos de previo/posterior
                                        Op-2 -> se muetra o no el de previo 
                                        -->
                            <?php

                            if ($pagina > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href='<?php echo "?pagina=$anterior"; ?>' aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php

                            if (isset($_GET["busqueda"])) {
                                // Se muestra la página activa y el total de la búsqueda
                                if ($paginas >= 1) {
                                    for ($x = 1; $x <= $paginas; $x++) {
                                        echo ($x == $pagina) ? "<li class='page-item active'><a class='page-link' href='?pagina=$x&busqueda={$_GET['busqueda']}'>$x</a></li>"
                                            : "<li class='page-item'><a class='page-link' href='?pagina=$x&busqueda={$_GET['busqueda']}'>$x</a></li>";
                                    }
                                }
                            } else {

                                // Se muestra la página activa y el total normal
                                if ($paginas >= 1) {
                                    for ($x = 1; $x <= $paginas; $x++) {
                                        echo ($x == $pagina) ? "<li class='page-item active'><a class='page-link' href='?pagina=$x'>$x</a></li>"
                                            : "<li class='page-item'><a class='page-link' href='?pagina=$x'>$x</a></li>";
                                    }
                                }
                            }


                            ?>
                            <!-- Op-2 -> se muestra o no el de anterior -->
                            <?php if ($pagina < $paginas) : ?>
                                <li class="page-item">
                                    <a class="page-link" href='<?php echo "?pagina=$siguiente"; ?>' aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>



                </div>
            <?php endif ?>
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
    <?php require_once "../inc/footer.inc"; ?>