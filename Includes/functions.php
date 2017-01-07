<?php

class Basic
{
    public $years_default = array(2016, 2017, 2018);
    public $estados_default = array(1=>'Activo', 2=>'Inactivo');
    public $meses_default = array(1=>"Ene", 2=>"Feb", 3=>"Mar", 4=>"Abr",
                                    5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Ago",
                                    9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec");
    public $size_years;
    public $size_meses;

    public function init(){
        //revisar tablas
        $this->revisar_tablas_basicas();
    }

    public function revisar_tablas_basicas(){
        global $db;
        //revisar tabla estados
        if($db->table_is_empty(Estado::$nombre_tabla)){
            Estado::insert_estados_default($this->estados_default);
        }

        //revisar tabla igss_salario_ordinario
        if($db->table_is_empty(Igss_Salario::$nombre_tabla)){
            Igss_Salario::insert_igss_salario_default($this->years_default);
        }
    }

    public function year_options($year_selected){
        $options = "";
        $years = Igss_Salario::get_years();
        foreach($years as $year){
            if($year_selected == $year){
                $options .= "<option value='{$year}' selected >{$year}</option>\n";
            } else {
                $options .= "<option value='{$year}'>{$year}</option>\n";
            }

        }
        return $options;
    }

    public function month_options($month_selected_num){
        $options = "";
        foreach($this->meses_default as $num => $month){
            if($month_selected_num == $num){
                $options .= "<option value='{$num}' selected>{$month}</option>";
            } else {
                $options .= "<option value='{$num}'>{$month}</option>";
            }
        }
        return $options;
    }

    public function redireccionar($location){
        header("Location: " . $location);
        exit;
    }

    public function guardar_isr_y_bonos($empleado, $fecha_inicio, $isr, $bonificacion){
        $inicio = new DateTime($fecha_inicio);
        $bono = new Bonificacion($empleado, $inicio, $bonificacion);
        $bono->guardar();
        $inicio = new DateTime($fecha_inicio);
        $isrr = new Isr($empleado, $inicio, $isr);
        $isrr->guardar();
    }

    public function my_is_numeric($value)//not used yet
    {
        return (preg_match ("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/", $value) == 1);
    }

}

$basic = new Basic();
$basic->init();
