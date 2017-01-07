<?php
class Isr
{
    public static $nombre_tabla = "isr";
    public static $campos = array('empleado_id', 'isr_fecha', 'isr_num');
    public $isr_id;
    public $empleado;
    public $isr_fecha;
    public $isr;

    function __construct($emp, $fecha, $isr)
    {
        $this->empleado = $emp;
        $this->isr_fecha = $fecha;
        $this->isr = $isr;
    }

    public static function get_user_isr($post){
        if(isset($post['isr'])){
            return self::valid_money($post['isr']);
        }
        return false;
    }

    public function guardar(){
        global $db;
        $emp_id = $this->empleado->empleado_id;
        $fecha_db_format = $this->isr_fecha->format('Y-m-d');
        $sql = 'INSERT INTO ' . self::$nombre_tabla . ' (';
        $sql .= $db->implode_fields(self::$campos) . ') values (';
        $sql .= "{$emp_id}, '{$fecha_db_format}', {$this->isr}";
        $sql .= ')';
        $db->query($sql);
    }

    public function editar_isr(){

    }

    public function get_ultimo_isr(){

    }

    /**
     * @param $empleado
     * @param $fecha_object
     * @return string
     * It returns the corresponding isr value of the Empleado(Object) and
     * fecha(Object) given
     */
    public static function get_corr_isr($empleado, $fecha_object){
        global $db;
        $empleado_id = $empleado->empleado_id;
        $fecha = $fecha_object->format('Y-m-d');
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE empleado_id = {$empleado_id} and ";
        $sql .= "isr_fecha <= '{$fecha}' ORDER BY isr_fecha DESC";
        $result = $db->query($sql);
        $val_isr = 0;
        while($row = $db->fetch_array($result)){
            $val_isr = $row['isr_num'];
        }
        return number_format($val_isr, 2, '.', '');
    }

    public static function get_isrs($empleado, $isr_id){
        global $db;
        $emp_id = $empleado->empleado_id;
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE empleado_id = {$emp_id}";
        if($isr_id != 'todos'){
            $sql .= " AND isr_id = {$isr_id} ";
        }
        $sql .= " ORDER by isr_fecha ASC";
        $result = $db->query($sql);
        $isrs = array();
        while($row = $db->fetch_array($result)){
            $i = new Isr($empleado, $row['isr_fecha'], number_format($row['isr_num'], 2, '.', ''));
            $i->isr_id = $row['isr_id'];
            $isrs[] = $i;
        }
        if($isr_id != 'todos'){
            return $isrs[0];
        }
        return $isrs;
    }

    public static function get_isrs_tabla($empleado){
        $isrs = self::get_isrs($empleado, 'todos');
        $tabla_filas = "";
        foreach($isrs as $isr){
            $row = '<tr>';
            $row .= "<td>{$isr->isr_fecha}</td>";
            $row .= "<td>{$isr->isr}</td>";
            $row .= "<td><a href='historial_isr.php?isr={$isr->isr_id}&id={$empleado->empleado_id}'>Modificar</a>";
            $row .= "&nbsp;&nbsp;<a href='historial_isr.php?isr=false&id={$empleado->empleado_id}&confirmar_eliminar_isr={$isr->isr_id}'>Eliminar</a></td>";
            $row .= '</tr>';
            $tabla_filas .= $row;
        }
        return $tabla_filas;
    }


    public static function eliminar($isr_id){
        global $db;
        $sql = "DELETE FROM " . self::$nombre_tabla . " ";
        $sql .= "WHERE isr_id = {$isr_id}";
        $db->query($sql);
    }

    public function editar($original){
        global $db;
        $id = $original->isr_id;
        $sql = 'UPDATE ' . self::$nombre_tabla . ' ';
        $sql .= "SET isr_num = {$this->isr} ";
        $sql .= "WHERE isr_id = {$id}";
        $db->query($sql);
    }

    public static function get_meses_con_isr($empleado_id){
        global $db;
        $sql = "select month(isr_fecha) as mes from isr WHERE empleado_id = {$empleado_id}";
        $result = $db->query($sql);
        $array = array();
        while($mes = $db->fetch_array($result)){
            $array[] = $mes['mes'];
        }
        return $array;
    }

    public static function mes_ya_existente($nueva_fecha, $array_meses_existentes){
        $nuevo_mes = $nueva_fecha->format('m');
        foreach($array_meses_existentes as $mes){
            if($mes == $nuevo_mes){
                return true;
            }
        }
        return false;
    }

    public function get_form_editar(){
        $form = "<h3><b>Editar: </b>{$this->isr_fecha}</h3>";
        $form .= "<form method='post' action='historial_isr.php?isr={$this->isr_id}&id={$this->empleado->empleado_id}'>";
        $form .= "<p>ISR:</p>";
        $form .= "Q.<input class='txtNum' type='text' name='isr' value='{$this->isr}'><br><br>";
        $form .= "<input type='submit' name='modificar_isr' value='Guardar Cambios'>";
        $form .= "</form>";
        return $form;
    }

    public static function get_user_fecha_isr($post){
        if($fecha = self::valid_date($post['fecha'])){
            return new DateTime($fecha);
        }
    }

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

    private static function valid_money($val){
        if(is_numeric($val)){
            return number_format($val, 2, '.', '');
        }
        return false;
    }

    public static function get_year_meses_con_isr($empleado_id){
        global $db;
        $sql = "SELECT EXTRACT( YEAR_MONTH FROM isr_fecha ) as year_mes FROM isr WHERE empleado_id = {$empleado_id}";
        $result = $db->query($sql);
        $array = array();
        while($year_mes = $db->fetch_array($result)){
            $array[] = $year_mes['year_mes'];
        }
        return $array;
    }

    public static function year_mes_ya_existente($nueva_fecha, $array_year_mes_existentes){
        $nuevo_year_mes = $nueva_fecha->format('Ym');
        foreach($array_year_mes_existentes as $year_mes){
            if($year_mes == $nuevo_year_mes){
                return true;
            }
        }
        return false;
    }
}