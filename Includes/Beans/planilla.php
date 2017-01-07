<?php
class Planilla
{
    public  $planilla;

    public function guardar(){
        foreach($this->planilla as $emp_planilla){
            $emp_planilla->guardar();
        }
    }

    public static function get_tabla_meses_con_planilla($year){
        global $basic;
        $rows = self::get_meses_con_planilla($year);
        $tabla = "<h3>Año: {$year}</h3>";
        $tabla .= "<table>";
        $tabla .= "<tr><th>Mes</th><th>Acción</th></tr>";

        foreach($rows as $row){
//            var_dump($row);
            $tabla .= "<tr>";
            $mes_nombre = $basic->meses_default[$row['mes']];
            $tabla .= "<td>{$mes_nombre}</td>";
            $tabla .= "<td><a href='mostrar_planilla.php?year={$year}&mes={$row['mes']}'>Mostrar</a> &nbsp; &nbsp;";
            $tabla .= "&nbsp; &nbsp;<a href='vista_consultar.php?action=confirmar_eliminar&year={$year}&mes={$row['mes']}'>Eliminar</a></td>";
            $tabla .= "</tr>";
        }
        $tabla .= "</table>";
        return $tabla;
    }

    private static function get_meses_con_planilla($year){
        global $db;
        $array = array();
        $sql = 'SELECT DISTINCT year, mes FROM ' . Planilla_Empleado::$nombre_tabla . ' ';
        $sql .= "WHERE year = {$year} ORDER BY mes ASC";
        $result = $db->query($sql);
        while($row = $db->fetch_array($result)){
            $row_year = $row['year'];
            $row_mes = $row['mes'];
            $array[] = array('year'=>$row_year, 'mes'=>$row_mes);
        }
        return $array;
    }

    public static function get_tabla_planilla($year, $mes){
        $planilla = self::get_planilla($year, $mes);
        $tabla = "";
        $sum_total_sueldo = 0;
        $sum_isr = 0;
        $sum_igss = 0;
        $sum_salario_liquido = 0;
        foreach($planilla->planilla as $row) {
            $tabla .= "<tr>";
            $tabla .= "<td>{$row->empleado->empleado_id}</td>";
            $tabla .= "<td>{$row->empleado->nombre_completo()}</td>";
            $tabla .= "<td>{$row->empleado->fecha_inicio_labores}</td>";
            $tabla .= "<td>Q.{$row->sueldo_ordinario}</td>";
            $tabla .= "<td>Q.{$row->bonificacion}</td>";
            $tabla .= "<td>Q.{$row->total_sueldo}</td>";
            $tabla .= "<td>Q.{$row->isr}</td>";
            $tabla .= "<td>Q.{$row->igss}</td>";
            $tabla .= "<td>Q.{$row->salario_liquido}</td>";
            $tabla .= "<td><a href='modificar_empleado_planilla.php?id={$row->planilla_empleado_id}'>Modificar</a>";
            $tabla .= "</tr>";
            $sum_total_sueldo += $row->total_sueldo;
            $sum_isr += $row->isr;
            $sum_igss += $row->igss;
            $sum_salario_liquido += $row->salario_liquido;
        }
        $tabla .= "<tr>";
        $tabla .= "<td></td><td><b>Totales</b></td><td></td><td></td><td></td>";
        $tabla .= "<td><b>Q.{$sum_total_sueldo}</b></td><td><b>Q.{$sum_isr}</b></td><td><b>Q.{$sum_igss}</b></td>
                    <td><b>Q.{$sum_salario_liquido}</b></td><td></td>";
        $tabla .= "</tr>";
        $tabla .= "</table>";
        $tabla .= "<h4>Guardada el: {$planilla->planilla[0]->fecha_generacion->format('Y-m-d')} </h4>";
        return $tabla;
    }

    private static function get_planilla($year, $month){
        global $db;
        $planilla = new self;
        $array = array();
        $sql = 'SELECT * FROM ' . Planilla_Empleado::$nombre_tabla . ' ';
        $sql .= "WHERE year = {$year} and mes = {$month}";
        $result = $db->query($sql);
        while($row = $db->fetch_array($result)){
            $e = new Planilla_Empleado();
            $e->planilla_empleado_id    = $row['planilla_empleado_id'];
            $e->year                    = $row['year'];
            $e->mes                     = $row['mes'];
            $empleado = Empleado::get_empleados($row['empleado_id']);
            $e->empleado                = $empleado;
            $e->sueldo_ordinario        = $row['sueldo_ordinario'];
            $e->bonificacion            = $row['bonificacion'];
            $e->isr                     = $row['isr'];
            $e->igss                    = $row['igss'];
            $e->total_sueldo            = $row['total_sueldo'];
            $e->salario_liquido         = $row['salario_liquido'];
            $fecha_creacion = new DateTime($row['fecha_generacion']);
            $e->fecha_generacion        = $fecha_creacion;
            $array[] = $e;
        }
        $planilla->planilla = $array;
        return $planilla;
    }

    public static function get_planilla_empleado_para_editar($id){
        global $db;
        $sql = "SELECT * FROM " . Planilla_Empleado::$nombre_tabla . " WHERE planilla_empleado_id = {$id} LIMIT 1";
        $result = $db->query($sql);
        $planilla_empleado = new Planilla_Empleado();
        while($row = $db->fetch_array($result)){
            $planilla_empleado->planilla_empleado_id    = $row['planilla_empleado_id'];
            $empleado = Empleado::get_empleados($row['empleado_id']);
            $planilla_empleado->empleado                = $empleado;
            $planilla_empleado->year                    = $row['year'];
            $planilla_empleado->mes                     = $row['mes'];
            $planilla_empleado->sueldo_ordinario        = $row['sueldo_ordinario'];
            $planilla_empleado->bonificacion            = $row['bonificacion'];
            $planilla_empleado->isr                     = $row['isr'];
            $planilla_empleado->igss                    = $row['igss'];
        }
        return $planilla_empleado;
    }

    public function confirmar_cambios(){}

    public static function get_user_sueldo_ordinario($post){
        if(isset($post['sueldo_ordinario'])){
            return self::valid_money($post['sueldo_ordinario']);
        }
        return false;
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

    public static function get_user_igss($post){
        if(isset($post['igss'])){
            return self::valid_money($post['igss']);
        }
        return false;
    }

    private static function valid_money($val){
        if(is_numeric($val)){
            return number_format($val, 2, '.', '');
        }
        return false;
    }

    public static function eliminar_planilla($year, $mes){
        global $db;
        $sql = 'DELETE FROM ' . Planilla_Empleado::$nombre_tabla . ' ';
        $sql .= "WHERE year={$year} and mes={$mes}";
        $db->query($sql);

    }

    public function ya_existente(){
        global $db;
        $year = $this->planilla[0]->year;
        $month = $this->planilla[0]->mes;
        $sql = "SELECT * FROM `planilla_empleado` WHERE year = {$year} and mes = {$month}";
        $result = $db->query($sql);
        $empty = $db->result_is_empty($result);
        return !$empty;
    }
}