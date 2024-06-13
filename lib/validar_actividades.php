<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../lib/config_conex.php";
require_once "../lib/date.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});

$encode = [];
if (isset($_POST["id_dia"])) {

    $id_dia = isset($_POST["id_dia"]) ? trim($_POST["id_dia"]) : null;
    $dia = isset($_POST["dia"]) ? trim($_POST["dia"]) : null;
    $manana = isset($_POST["manana"]) ? $_POST["manana"] : null;
    $media_m = isset($_POST["media_m"]) ? $_POST["media_m"] : null;
    $ult_m = isset($_POST["ult_m"]) ? $_POST["ult_m"] : null;
    $tarde = isset($_POST["tarde"]) ? $_POST["tarde"] : null;
    $media_t = isset($_POST["media_t"]) ? $_POST["media_t"] : null;
    $ult_t = isset($_POST["ult_t"]) ? $_POST["ult_t"] : null;


    if (
        $id_dia  === "default" || $manana === "default" || $media_m === "default"
        || $ult_m === "default" || $tarde === "default" || $media_t === "default" || $ult_t === "default"
    ) {
        $encode = ["error" => true, "msg" => "no pueden quedar parámetros vacíos"];
        exit;
    } else {

        $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        $db->setConsulta("UPDATE actividades
                        SET 
                        manana = ?,
                        media_m = ?,
                        ult_m = ?,
                        tarde = ?,
                        media_t = ?,
                        ult_t = ?
                        WHERE actividades = ?");
        $db->setParam()->bind_param("ssssssi", $manana, $media_m, $ult_m, $tarde, $media_t, $ult_t,  $id_dia);
        if (!$db->ejecutar()) {
            $encode = ["error" => true, "msg" => "ha fallado la ejecución"];
            exit;
        } else {
            $encode = ["error" => false, "msg" => "La programación del $dia ha sido registrada"];
            $db->cerrar();
        }
    }
} else {
    $encode = ["error" => true, "msg" => "El post no ha sido realizado"];
    exit;
}

echo json_encode($encode);
exit;
