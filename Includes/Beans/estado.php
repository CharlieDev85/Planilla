<?php

class Estado
{
    public static $nombre_tabla = "estado";
    public static $campos = array('estado_id', 'estado');
    public $estado_id;
    public $estado;


    function __construct($estado_id, $estado){
        $this->estado_id = $estado_id;
        $this->estado = $estado;
    }

    function __toString(){
        return $this->estado;
    }

    public function get_estados_db(){
        global $db;
        $table = self::$nombre_tabla;
        $sql = "SELECT * FROM {$table}";
        $result = $db->query($sql);

    }

    public function save(){
        global $db;
        $fields = $db->implode_fields(self::$campos);
        $sql = "INSERT INTO {$this::$nombre_tabla} ";
        $sql .= "({$fields}) ";
        $sql .= "values ({$this->estado_id}, '{$this->estado}')";
        $db->query($sql);
    }

    public static function insert_estados_default($estados_default){
        foreach($estados_default as $key => $value){
            $estado = new Estado($key, $value);
            $estado->save();
        }
    }

}
