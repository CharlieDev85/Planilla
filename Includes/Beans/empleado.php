<?php
class Empleado
{
    public static $regex_name = '/^[a-zA-Z0-9 ÁÉÍÓÚáéíóúñÑüÜ]+$/';
    public static $nombre_tabla = "empleado";
    public static $campos = array('nombre', 'apellido', 'fecha_inicio_labores', 'estado_id', 'fecha_inactividad');
    public $empleado_id;
    public $nombre;
    public $apellido;
    public $fecha_inicio_labores;
    public $estado;
    public $fecha_inactividad;


    /**
     * @param $id
     * @return array|mixed
     * returns an Empleado when id is specified
     * returns all Empleados when string 'todos' is given as parameter
     */
    public static function get_empleados($id){
        global $db;
        global $basic;
//        $basic = new Basic();
        $table = self::$nombre_tabla;
        $sql = "SELECT * FROM {$table}";
        if($id != 'todos'){
            $sql .= " WHERE empleado_id ={$id} LIMIT 1";
        }
        $result = $db->query($sql);
        $empleados = array();
        while($row = $db->fetch_array_assoc($result)){
            $e = new Empleado();
            $e->empleado_id = $row['empleado_id'];
            $e->nombre = $row['nombre'];
            $e->apellido = $row['apellido'];
            $e->fecha_inicio_labores = $row['fecha_inicio_labores'];
            $e->estado = $basic->estados_default[$row['estado_id']];
            $e->fecha_inactividad = $row['fecha_inactividad'];
            $empleados[] = $e;
        }
        if($id != 'todos'){
            return $empleados[0];
        }
        return $empleados;
    }

    public static function get_empleados_para_tabla(){
        $empleados = self::get_empleados('todos');
        $empleados_tabla = "";
        foreach($empleados as $e){
            $row = "";
            $row .= "<tr>";
            $row .= "<td>{$e->empleado_id}</td>";
            $row .= "<td>{$e->nombre_completo()}</td>";
            $row .= "<td>{$e->fecha_inicio_labores}</td>";
            $row .= "<td>{$e->estado}</td>";
            $inactividad = ($e->estado == 'Activo'? "N/A": $e->fecha_inactividad);
            $row .= "<td>{$inactividad}</td>";
            $fecha_object = new DateTime();
            $row .= '<td>Q.' . $e->get_valor_bonificacion($fecha_object) . '</td>';
            $row .= '<td>Q.' . $e->get_valor_isr($fecha_object) . '</td>';
            $row .= "<td><a href='modificar_empleado.php?id={$e->empleado_id}'>Modificar</a>&nbsp;/&nbsp;";
            $row .= "<a href='vista_empleados.php?confirmar_eliminar={$e->empleado_id}'>Eliminar</a></td>";
            $row .= "</tr>\n";
            $empleados_tabla .= $row;
        }
        return $empleados_tabla;
    }

    public function get_valor_bonificacion($fecha_object){
        return Bonificacion::get_corr_bonificacion($this, $fecha_object);
    }

    public function get_valor_isr($fecha_object){
        return Isr::get_corr_isr($this, $fecha_object);
    }

    private static function valid_text($string){
        //return ctype_alpha(str_replace(' ', '', $string));
        return preg_match(self::$regex_name, $string);
    }

    private static function valid_date($string){
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

    public static function get_user_nombre($post){
        $nombre = false;
        if(isset($post['nombre'])){
            if(self::valid_text($post['nombre'])){
                $nombre = $post['nombre'];
            }
        }
        return  $nombre;
    }

    public static function get_user_apellido($post){
        $apellido = false;
        if(isset($post['apellido'])){
            if(self::valid_text($post['apellido'])){
                $apellido = $post['apellido'];
            }
        }
        return $apellido;
    }

    public static function get_user_fecha_inicio($post){
        return self::valid_date($post['fecha_inicio']);
    }

    public static function get_user_estado($post){
        $estado = false;
        if(isset($post['estado'])){
            if(intval($post['estado'] != 0)){
                $estado = $post['estado'];
            }
        }
        return $estado;
    }

    public static function get_user_bonificacion($post){
        if(isset($post['bonificacion'])){
            return self::valid_money($post['bonificacion']);
        }
        return false;
    }

    public static function get_user_isr($post){
        if(isset($post['isr'])){
            return self::valid_money($post['isr']);
        }
        return false;
    }

    public static function get_user_fecha_inactividad($post){
        //return self::valid_date($post['fecha_inactividad']);
        //it's returning false
        if(!isset($post['fecha_inactividad']) ){
            return true;
        }
        if($post['fecha_inactividad'] == ''){
            return false;
        }
        return self::valid_date($post['fecha_inactividad']);;
    }

    public function confirmar_cambios(){

    }

    public static function eliminar($id){
        global $db;
        $sql = 'DELETE FROM ' . self::$nombre_tabla . ' ';
        $sql .= "WHERE empleado_id = {$id}";
        $db->query($sql);
    }

    public function editar($original){
        global $db;
        $id = $original->empleado_id;
        $fecha_inactividad = $this->estado != 1? $this->fecha_inactividad: 'null';
        $sql = 'UPDATE ' . self::$nombre_tabla . ' ';
        $sql .= "SET nombre='{$this->nombre}', apellido='{$this->apellido}', fecha_inicio_labores='{$this->fecha_inicio_labores}', ";
        if($fecha_inactividad == 'null'){
            $sql .= "estado_id = {$this->estado}, fecha_inactividad=null";
        } else {
            $sql .= "estado_id = {$this->estado}, fecha_inactividad='{$fecha_inactividad}'";
        }
        $sql .= " WHERE empleado_id={$id}";
        $db->query($sql);
    }

    public function guardar(){
        global $db;
        $sql = 'INSERT INTO ' . self::$nombre_tabla . ' ';
        $sql .= '(' . $db->implode_fields(self::$campos) . ') values (';
        if($this->estado == 1){
            $sql .= "'{$this->nombre}', '$this->apellido', '{$this->fecha_inicio_labores}', 1, null";
        }elseif ($this->estado == 2) {
            $sql .= "'{$this->nombre}', '$this->apellido', '{$this->fecha_inicio_labores}', 2, '{$this->fecha_inactividad}'";
        }
        $sql .= ')';
        $db->query($sql);
        $this->empleado_id = $db->get_last_id_inserted();
    }

    public function nombre_completo(){
        return $this->nombre. ' ' . $this->apellido;
    }
}