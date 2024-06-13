<?php
//require "../lib/errores.php"; // ---> !important

class Database
{
    public $db;
    protected $consulta;
    protected $param;
    protected $resultado;

    // Función constructora
    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port )
    {
        $this->db = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port );

        if ($this->db->connect_errno) {
            trigger_error("Fallo al realizar la conexión, Tipo de error -> ({$this->db->connect_error})", E_USER_ERROR);
        }

        $this->db->set_charset(DB_CHARSET);
    }

    // Obtiene todos los datos de la tabla socios
    // No requiere de ningún parámetro
    public function getSocios()
    {
        $this->resultado = $this->db->query("SELECT * FROM socios;"); // instancia $resultado
        return $this->resultado->fetch_all(MYSQLI_ASSOC); // Devuelte todas las filas consultadas en un 
        // array asociativo, numérico o ambos.
    }

    // Obtiene un cliente en array asociativo
    public function getSocio()
    {
        return $this->resultado->fetch_assoc();
    }

    // Prepara la consulta a la base de datos
    // con parametros proporcionados en param
    public function setConsulta($consulta)
    {
        $this->consulta = $consulta;
        if (strpos($this->consulta, '?') !== false) {
            // Solo preparar la consulta si contiene parámetros
            $this->param = $this->db->prepare($this->consulta);
            if (!$this->param) {
                trigger_error("Error al preparar la consulta: " . $this->db->error, E_USER_ERROR);
                return false;
            }
        } else {
            $this->param = null; // No hay parámetros, ejecutar directamente
        }
        return true;
    }

    // agrega los parametro/s a la variable param
    public function setParam()
    {
        return $this->param;
    }

    // Devuelte una fila de un resultado de la consulta 
    // como un array asociativo
    public function getResultado()
    {
        return $this->resultado->fetch_assoc();
    }

    // Obtiene todos los datos de un socio por su id
    // 
    public function getSocioPorId($id)
    {
        $this->param = $this->db->prepare("SELECT 
                                            nombre, 
                                            apellido, 
                                            email, 
                                            saldo, 
                                            FROM socios 
                                            WHERE id_socio = ?");
        $this->param->bind_param('i', $id);
        if ($this->param->execute()) {
            $this->resultado = $this->param->get_result();
            return $this->resultado->fetch_assoc();
        } else {
            trigger_error("Error al ejecutar la consulta: " . $this->param->error, E_USER_ERROR);
            return false;
        }
    }

    // ejecuta la consulta a la bd
    public function ejecutar()
    {
        if ($this->param === null) {
            // Ejecutar directamente si no hay parámetros
            $this->resultado = $this->db->query($this->consulta);
            if ($this->resultado) {
                return true;
            } else {
                trigger_error("Error al ejecutar la consulta: " . $this->db->error, E_USER_ERROR);
                return false;
            }
        } else {
            // Ejecutar con parámetros
            if ($this->param->execute()) {
                $this->resultado = $this->param->get_result();
                return true;
            } else {
                trigger_error("Error al ejecutar la consulta: " . $this->param->error, E_USER_ERROR);
                return false;
            }
        }
    }

    // Devuelve la/s fila/s sobre la que 
    // se aplique algún cambio
    public function getFilasAfectadas(){
        return $this->param->affected_rows;
    }

    // Método para contar filas resultantes de una consulta
    public function contarFilasConsulta($consulta)
    {
        $resultado = $this->db->query($consulta);
        if ($resultado) {
            $contador = $resultado->num_rows;
            $resultado->free();
            return $contador;
        } else {
            trigger_error("Error al ejecutar la consulta: " . $this->db->error, E_USER_ERROR);
            return false;
        }
    }


    // Libera la memoria asociada a un 
    // resultado 
    public function despejar()
    {
        if ($this->resultado) {
            $this->resultado->free();
            $this->resultado = null;
        }
        if ($this->param) {
            $this->param->close();
            $this->param = null;
        }
    }
    // Cierra la conexión abierta de una BD
    // instanciada
    public function cerrar()
    {
        $this->db->close();
        $this->param->close();
    }

    // Confirma la existencia de una columna concreta
    // dada una condición
    // Devuelve un integer con el número de filas
    public function validarDatos($columna, $tabla, $condicion)
    {
        $stmt = $this->db->prepare("SELECT $columna FROM $tabla WHERE $columna = ?");
        $stmt->bind_param('s', $condicion);
        $stmt->execute();
        $this->resultado = $stmt->get_result();
        return $this->resultado->num_rows;
    }

    // Posibilita el cambio de una BD a otra
    public function cambiarDatabase($db)
    {
        $this->db->select_db($db);
    }
//encriptar CONTRASEÑAS
// protected function encriptar($string){
//     return md5($string);
// }
}

?>