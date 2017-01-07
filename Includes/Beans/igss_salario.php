<?php
class Igss_Salario
{
    public static $nombre_tabla = "igss_salario";
    public static $campos = array('year', 'igss', 'salario_ordinario');
    public $igss_salario_ordinario_id;
    public $year;
    public $cuota_igss = 100.000;
    public $salario_ordinario = 2200.000;


    function __construct($year){
        $this->year = $year;
    }


    public function save(){
        global $db;
        $year_exists_in_db = $this->year_exists_in_db($this->year);
        if(!$year_exists_in_db){
            $tabla = self::$nombre_tabla;
            $fields = $db->implode_fields($this::$campos);
            $sql = "INSERT INTO {$tabla} ";
            $sql .= "($fields) ";
            $sql .= "values ({$this->year}, {$this->cuota_igss}, {$this->salario_ordinario})";
            $db->query($sql);
            return $db->affected_rows();
        }
        return false;
    }

    private function year_exists_in_db($year){
        global $db;
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE year = {$year}";
        $result = $db->query($sql);
        return !$db->result_is_empty($result);
    }

    public static function insert_igss_salario_default($years_default){
        foreach($years_default as $year){
            $igss_salario = new Igss_Salario($year);
            $igss_salario->save();
        }
    }

    /**
     * @param $year
     * @return Igss_Salario
     * It retuns an Igss_Salario object depending of year given.
     */
    public static function get_object($year){
        global $db;
        $igss_salario = new Igss_Salario($year);
        $n = self::$nombre_tabla;
        $sql = "SELECT DISTINCT * FROM {$n} ";
        $sql .= "WHERE year = {$year}";
        $result = $db->query($sql);
        while($row = $db->fetch_array($result)){
            $igss_salario->cuota_igss           = number_format($row['igss'], 2, '.', '');
            $igss_salario->salario_ordinario    = number_format($row['salario_ordinario'], 2, '.', '');
        }
        return $igss_salario;
    }

    public static function get_years(){
        global $db;
        $sql = 'SELECT year FROM ' . self::$nombre_tabla . ' ';
        $result = $db->query($sql);
        $years = array();
        while($year = $db->fetch_array($result)){
            $years[] = $year['year'];
        }
        return $years;
    }

    public static function get_user($val){
        if(is_numeric($val)){
            return number_format($val, 2, '.', '');
        }
        return false;
    }


    public function actualizar(){
        global $db;
        $year = $this->year;
        $igss = $this->cuota_igss;
        $salario_ordinario = $this->salario_ordinario;
        $sql = 'UPDATE ' . self::$nombre_tabla . ' ';
        $sql .= "SET igss={$igss}, salario_ordinario={$salario_ordinario} ";
        $sql .= "WHERE year={$year}";
        $db->query($sql);
        return $db->affected_rows();
    }

    private static function get_all(){
        global $db;
        $sql = 'SELECT * FROM ' . self::$nombre_tabla . ' ';
        $result = $db->query($sql);
        $array = array();
        while($row = $db->fetch_array($result)){
            $igss_salario                       = new self($row['year']);
            $igss_salario->igss_salario_ordinario_id = $row['igss_salario_id'];
            $igss_salario->cuota_igss           = number_format($row['igss'],2,'.', '');
            $igss_salario->salario_ordinario    = number_format($row['salario_ordinario'], 2, '.', '');
            $array[] = $igss_salario;
        }
        return $array;
    }

    public static function get_tabla(){
        $rows = self::get_all();
        $tabla = '';
        foreach($rows as $row){
            $tabla .= "<tr>";
            $tabla .= "<td>{$row->year}</td>";
            $tabla .= "<td>Q.{$row->cuota_igss}</td>";
            $tabla .= "<td>Q.{$row->salario_ordinario}</td>";
            $tabla .= "<td><a href='modificar_igss_salario_ordinario.php?modificar={$row->year}'>Modificar</a>";
            $tabla .= "&nbsp;&nbsp;<a class='eliminar_year' href='vista_igss_salario_ordinario.php?confirmar_eliminar={$row->year}'>Eliminar</a></td>";
            $tabla .= "</tr>\n";
        }
        return $tabla;
    }

    public static function eliminar($year){
        global $db;
        $sql = 'DELETE FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE year = {$year}";
        $db->query($sql);
    }

}