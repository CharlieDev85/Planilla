<?php

/**
 * Class Bonificacion
 */
class Bonificacion
{
    public static $nombre_tabla = "bonificacion";
    public static $campos = array('empleado_id', 'bonificacion_fecha', 'bonificacion_num');
    public $bonificacion_id;
    public $empleado;
    public $bonificacion_fecha;
    public $bonificacion;

    function __construct($empleado, $bono_fecha, $bono){
        $this->empleado             = $empleado;
        $this->bonificacion_fecha   = $bono_fecha;
        $this->bonificacion         = $bono;
    }

    /**
     * It retuns an array with all the Years and Months that have "bonos" in the database.
     * The result is the format YYYYMM
     * @param $empleado_id
     * @return array
     */
    public static function get_year_meses_con_bonos($empleado_id){
        global $db;
        $sql = "SELECT EXTRACT( YEAR_MONTH FROM bonificacion_fecha ) as year_mes FROM bonificacion WHERE empleado_id = {$empleado_id}";
        $result = $db->query($sql);
        $array = array();
        while($year_mes = $db->fetch_array($result)){
            $array[] = $year_mes['year_mes'];
        }
        return $array;
    }

    /**
     * year_mese_ya existente
     *
     * It compares a given date with an array of years and months. If the given date is found in the array it will
     * return true, otherwise it willl return false.
     *
     * @param $nueva_fecha
     * @param $array_year_mes_existentes
     * @return bool
     */
    public static function year_mes_ya_existente($nueva_fecha, $array_year_mes_existentes){
        $nuevo_year_mes = $nueva_fecha->format('Ym');
        foreach($array_year_mes_existentes as $year_mes){
            if($year_mes == $nuevo_year_mes){
                return true;
            }
        }
        return false;
    }

    /**
     *It takes the current "bonificacion" and save it into the database.
     */
    public function guardar(){
        global $db;
        $empleado_id = $this->empleado->empleado_id;
        $fecha_db_format = $this->bonificacion_fecha->format('Y-m-d');
        $sql = 'INSERT INTO ' . self::$nombre_tabla . ' ';
        $sql .= '(' . $db->implode_fields(self::$campos) . ') values (';
        $sql .= "{$empleado_id}, '{$fecha_db_format }', {$this->bonificacion}";
        $sql .= ')';
        $db->query($sql);
    }


    /**
     * If bono_id is given it will return the Bonificacion that has the given bono_id.
     * @param $empleado
     * @param $bono_id (String) It could be "todos" or a bono_id
     * @return array|mixed
     */
    public static function get_bonificaciones($empleado, $bono_id){
        global $db;
        $emp_id = $empleado->empleado_id;
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE empleado_id = {$emp_id}";
        if($bono_id != 'todos'){
            $sql .= " AND bonificacion_id = {$bono_id} ";
        }
        $sql .= " ORDER by bonificacion_fecha ASC";
        $result = $db->query($sql);
        $bonos = array();
        while($row = $db->fetch_array($result)){
            $b = new Bonificacion($empleado, $row['bonificacion_fecha'], number_format($row['bonificacion_num'], 2, '.', ''));
            $b->bonificacion_id = $row['bonificacion_id'];
            $bonos[] = $b;
        }
        if($bono_id != 'todos'){
            return $bonos[0];
        }
//        var_dump($bonos);
        return $bonos;
    }

    /**
     * @param $empleado
     * @return string
     */
    public static function get_bonificaciones_tabla($empleado){
        $bonos = self::get_bonificaciones($empleado, 'todos');
//        var_dump($bonos);
        $tabla_filas = "";
        foreach($bonos as $bono){
            $row = '<tr>';
            $row .= "<td>{$bono->bonificacion_fecha}</td>";
            $row .= "<td>Q.{$bono->bonificacion}</td>";
            $row .= "<td><a href='historial_bonificaciones.php?bono={$bono->bonificacion_id}&id={$empleado->empleado_id}'>Modificar</a>";
            $row .= "&nbsp;&nbsp;<a href='historial_bonificaciones.php?bono=false&id={$empleado->empleado_id}&confirmar_eliminar_bono={$bono->bonificacion_id}'>Eliminar</a></td>";
            $row .= '</tr>';
            $tabla_filas .= $row;
        }
        return $tabla_filas;
    }

    /**
     * @param $original
     */
    public function editar($original){
        global $db;
        $id = $original->bonificacion_id;
        $sql = 'UPDATE ' . self::$nombre_tabla . ' ';
        $sql .= "SET bonificacion_num = {$this->bonificacion} ";
        $sql .= "WHERE bonificacion_id = {$id}";
        $db->query($sql);
    }

    /**
     *
     */
    public function get_ultima_bonificacion(){

    }

    /**
     * @param $empleado
     * @param $fecha_object
     * @return string
     */
    public static function get_corr_bonificacion($empleado, $fecha_object){
        global $db;
        $empleado_id = $empleado->empleado_id;
        $fecha_string = $fecha_object->format('Y-m-d');
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE empleado_id = {$empleado_id} and ";
        $sql .= "bonificacion_fecha <= '{$fecha_string}' ";
        $sql .= "ORDER BY bonificacion_fecha DESC";
        $result = $db->query($sql);
        $bono = 0;
        while($row = $db->fetch_array($result)){
            $bono = $row['bonificacion_num'];
        }
        return number_format($bono, 2, '.', '');
    }

    /**
     * @return string
     */
    public function get_form_editar(){
        $form = "<h3><b>Editar: </b>{$this->bonificacion_fecha}</h3>";
        $form .= "<form method='post' action='historial_bonificaciones.php?bono={$this->bonificacion_id}&id={$this->empleado->empleado_id}'>";
        $form .= "<p>Bonificación:</p>";
        $form .= "Q.<input class='txtNum' type='text' name='bono' value='{$this->bonificacion}'><br><br>";
        $form .= "<input type='submit' name='modificar_bono' value='Guardar Cambios'>";
        $form .= "</form>";
        return $form;
    }


    /**
     * @param $bono_id
     */
    public static function eliminar($bono_id){
        global $db;
        $sql = "DELETE FROM " . self::$nombre_tabla . " ";
        $sql .= "WHERE bonificacion_id = {$bono_id}";
        $db->query($sql);
    }

    /**
     * @param $post
     * @return DateTime
     */
    public static function get_user_fecha_bonificacion($post){
        if($fecha = self::valid_date($post['fecha'])){
            return new DateTime($fecha);
        }
    }

    /**
     * @param $string
     * @return bool|string
     * If the given string is a valid date, it will return the date itself. If the given string is not a valid date,
     * it will return false. The accepted formats are: YYYY-MM-DD or MM/DD/YYYY (make sure)
     */
    public static function valid_date($string){
        $date = explode('-', $string);
        $date_firefox = explode('/', $string);
        if(isset($date[0]) && isset($date[1]) && isset($date[2])){
            $valid_date = checkdate($date[1], $date[2], $date[0]);
            if($valid_date){
                return $string;
            } else{
                return false;
            }
        } elseif(isset($date_firefox[0]) && isset($date_firefox[1]) && isset($date_firefox[2])){
            $valid_date_firefox = checkdate($date_firefox[0], $date_firefox[1], $date_firefox[2]);
            if($valid_date_firefox){
                return $date_firefox[2]. '-' . $date_firefox[0] . '-' . $date_firefox[1];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $post
     * @return bool|string
     */
    public static function get_user_bono($post){
        if(isset($post['bono'])){
            return self::valid_money($post['bono']);
        }
        return false;
    }

    /**
     * @param $val
     * @return bool|string
     */
    private static function valid_money($val){
        if(is_numeric($val)){
            return number_format($val, 2, '.', '');
        }
        return false;
    }
}