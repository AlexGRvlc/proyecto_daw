<?php 

require_once "../lib/config_conex.php";
require_once "../lib/date.php";
spl_autoload_register(function ($clase) {
    require_once "../lib/$clase.php";
});

$output = [];
if(isset($_POST["id"])){

    $db_id = $_POST["id"];
    $nombre_edit = $_POST["nombre"];
    $apellido_edit = $_POST["apellido"];
    $email_edit = $_POST["email"];
    $saldo_edit = $_POST["saldo"];

    
    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    $db->setConsulta("UPDATE socios
                    SET 
                    nombre = ?,
                    apellido = ?,
                    email = ?,
                    saldo = ?
                    WHERE id = ?");
    $db->setParam()->bind_param("sssii", $nombre_edit, $apellido_edit, $email_edit, $saldo_edit, $db_id);
    $db->ejecutar();
    $db->despejar();

    $output = ["error" => false, "tipo_error" => "Socio actualizado"];

    header("Refresh:1; url=../pages/editar_socios.php");

}else {
    $output = ["error" => true, "tipo_error" => "Ha habido un problema con la llamada a la BD"];
}

echo json_encode($output);

?>