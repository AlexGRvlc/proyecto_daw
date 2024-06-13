<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia el almacenamiento en búfer de salida
ob_start();

require_once "../lib/config_conex.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});

$output = [];
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if (isset($_POST["eliminar"])) {
    $eliminar = $_POST["eliminar"];

    $db->setConsulta("DELETE FROM socios WHERE id = ?");
    $db->setParam()->bind_param('i', $eliminar);
    $db->ejecutar();

    if ($db->getFilasAfectadas() > 0) {
        $output = ["estado" => "ok", "msg" => "Socio Eliminado"];
    } else {
        $output = ["estado" => "fail", "msg" => "Hubo un error inesperado"];
    }
    $db->despejar();
} else {
    $output = ["estado" => "fail", "msg" => "No se recibió el parámetro 'eliminar'"];
}

// Limpia el búfer de salida y desactiva el almacenamiento en búfer
ob_end_clean();

// Configura la cabecera para enviar JSON
header('Content-Type: application/json');

// Envía la respuesta JSON
echo json_encode($output);
exit;
?>
