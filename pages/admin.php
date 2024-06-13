<?php
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

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); // instanciando clase mysqli

$socio_id = $_SESSION["id_socio"]; // Sacar la id del usuario para parámetro consulta

// # Total de socios
$db->setConsulta("SELECT
COUNT(id) AS total_socios
FROM socios");
$db->ejecutar();
$resultado = $db->getResultado();
$total_socios = $resultado["total_socios"]; // cifra sacar x pantalla
$db->despejar();

// Se requiere el nombre de usuario para
// el panel/pantalla de registrad@
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

// Para sacar variables a mostrar en el panel de administrador/a
// de la info de los últimos socios registrados
$db->setConsulta("SELECT 
                CONCAT (nombre, ' ', apellido)  AS nombre_completo, 
                email, 
                saldo, 
                fecha 
                FROM socios 
                ORDER BY fecha DESC LIMIT 10 ");
$db->ejecutar();

?>

<!-- Complemento para proteger sesión admin -->
<?php if ($_SESSION["rol"] == "administrador") : ?>


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
                    <li><a href="editar_socios.php">Editar</a></li>
                    <li><a href="semanal.php">Semanal</a></li>
                    <li><a id="logout" class="logout" href="#">Cerrar Sesión</a></li>
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
                    <h2>Administración</h2>
                    <div id="fecha">
                        <i class="bi bi-calendar3"></i>
                        <span><?php echo "$dia $dia_date $mes, $anyo"; ?></span>
                    </div>
                </div>
                <div id="total-socios">
                    <div id="admin-usr-img">
                        <img src="../public/imgs/user.svg" alt="">
                    </div>
                    <div class="socios-info">
                        <h1><?php echo $total_socios; ?></h1>
                        <h3>Socios</h3>
                    </div>
                </div>

                <div class="table-responsive">
                    <h3>Últimos socios registrados</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class='sm-hide'>Email</th>
                                <th class='sm-hide'>Saldo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Salida por pantalla de la info de socio
                            $contador = 0;
                            while ($row = $db->getResultado()) {
                                $contador++;
                                $fechaFormateada = date('d - m - Y', $row['fecha']);
                                echo "<tr>
                                    <td>$contador</td>
                                    <td>{$row['nombre_completo']}</td>
                                    <td class='sm-hide' >{$row['email']}</td>
                                    <td class='sm-hide' >{$row['saldo']}</td>                 
                                    <td  >$fechaFormateada</td>                 
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


        <?php else : ?>

            <?php header("Location: socios.php"); ?>

        <?php endif; ?>

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