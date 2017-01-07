<?php
class Planilla_Empleado
{
    public static $nombre_tabla = "planilla_empleado";
    public $planilla_empleado_id;
    public $year;
    public $mes;
    public $empleado;
    public $sueldo_ordinario;
    public $bonificacion;
    public $isr;
    public $igss;
    public $total_sueldo;
    public $salario_liquido;
    public $fecha_generacion;

    public static function get_tabla_y_planilla($year, $month){
        global $basic;
        $rows = self::get_planilla_empleados($year, $month);
        if($rows){
            $mes_nombre = $basic->meses_default[$month];
            $tabla = "<form action='vista_generar.php?action=false&year={$year}&month={$month}&generar=guardar' method='post'>";
            $tabla .= "<hr/><h4 style='display:inline-block'>Planilla: {$mes_nombre} {$year}&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</h4>";
            $tabla .= "<button name='guardar_planilla' value='true'>Guardar Planilla</button>";
            $tabla .= "</form>";
            $tabla .= "<table>";
            $tabla .= "<tr>";
            $tabla .= "<th>No.</th><th>Nombre</th><th>Inicio</th><th>Sueldo Ordinario</th><th>Bonificación</th>";
            $tabla .= "<th>Total Sueldo</th><th>ISR</th><th>IGSS</th><th>Salario Liquido</th>";
            $tabla .= "</tr>";

            foreach($rows as $emp_planilla){
                $r = "<tr>";

                $r .= "<td>{$emp_planilla->empleado->empleado_id}</td>";
                $r .= "<td>{$emp_planilla->empleado->nombre_completo()}</td>";
                $r .= "<td>{$emp_planilla->empleado->fecha_inicio_labores}</td>";
                $r .= "<td>Q.{$emp_planilla->sueldo_ordinario}</td>";
                $r .= "<td>Q.{$emp_planilla->bonificacion}</td>";
                $r .= "<td>Q.{$emp_planilla->total_sueldo}</td>";
                $r .= "<td>Q.{$emp_planilla->isr}</td>";
                $r .= "<td>Q.{$emp_planilla->igss}</td>";
                $r .= "<td>Q.{$emp_planilla->salario_liquido}</td>";

                $r .= "</tr>";
                $tabla .= $r;
            }
            $tabla .= "</table>";
            $final_result = array();
            $final_result['tabla'] = $tabla;
            $final_result['planilla'] = $rows;
            return $final_result;
        } else {
            return false;
        }

    }

    public static function get_selected_year($get){
        $year = intval($get['year']);
        if($year != 0){
            return $year;
        }
        return false;
    }

    public static function get_selected_month($get){
        $month = intval($get['month']);
        if($month != 0){
            return $month;
        }
        return false;
    }

    public function add_to_planilla_temp(){}

    public static function get_planilla_empleados($year, $mes){
        global $db;
        $sql = "CALL generar_planilla5('{$mes}', '{$year}');";
        $result = $db->query_call($sql);
//        var_dump($result);

        if(!$db->result_is_empty($result)){
            $rows = array();
            while($row = $db->fetch_array($result)){
//            var_dump($row);
                $empleado = new Empleado();
                $empleado->empleado_id = $row['empleado_id'];
                $empleado->nombre = $row['nombre'];
                $empleado->apellido = $row['apellido'];
                $empleado->fecha_inicio_labores = $row['fecha_inicio_labores'];
                $empleado_planilla = new self;
                $empleado_planilla->year = $year;
                $empleado_planilla->mes = $mes;
                $empleado_planilla->empleado = $empleado;
                $empleado_planilla->sueldo_ordinario = number_format($row['salario_ordinario'], 2,'.', '');
                $empleado_planilla->igss = number_format($row['igss'], 2,'.', '');
                $empleado_planilla->isr = number_format($row['isr_num'], 2, '.', '');
                $empleado_planilla->bonificacion = number_format($row['bonificacion_num'], 2, '.', '');
                $empleado_planilla->total_sueldo = number_format($row['total_sueldo'], 2, '.', '');
                $empleado_planilla->salario_liquido = number_format($row['salario_liquido'], 2, '.', '');
                $empleado_planilla->fecha_generacion = new DateTime();
                $rows[] = $empleado_planilla;
            }
            return $rows;
        } else {
            return false;
        }

    }

    public function guardar(){
        global $db;
        $sql = 'INSERT INTO ' . self::$nombre_tabla . ' ';
        $sql .= "(year, mes, empleado_id, sueldo_ordinario, bonificacion, isr, total_sueldo, igss, salario_liquido, fecha_generacion)";
        $sql .= " values (";
        $sql .= "{$this->year}, {$this->mes}, {$this->empleado->empleado_id}, {$this->sueldo_ordinario}, ";
        $sql .= "{$this->bonificacion}, {$this->isr}, {$this->total_sueldo}, {$this->igss}, ";
        $sql .= "{$this->salario_liquido}, '{$this->fecha_generacion->format('Y-m-d')}'";
        $sql .= ")";
        echo $sql;
        $db->query($sql);
    }

    public function modificar(){
        global $db;
        $sql = 'UPDATE ' . Planilla_Empleado::$nombre_tabla . ' ';
        $sql .= "SET sueldo_ordinario={$this->sueldo_ordinario}, bonificacion={$this->bonificacion}, isr={$this->isr}, igss={$this->igss}";
        $sql .= "WHERE planilla_empleado_id = {$this->planilla_empleado_id}";
        $db->query($sql);

    }

}