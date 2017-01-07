<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 29/10/2016
 * Time: 12:12 PM
 */

require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");

if(sizeof($_POST) > 0){
    global $basic;
    global $db;
    $nombre = Empleado::get_user_nombre($_POST);
    $apellido = Empleado::get_user_apellido($_POST);
    $estado = Empleado::get_user_estado($_POST);
    $fecha_inicio = Empleado::get_user_fecha_inicio($_POST);
    $fecha_inactividad = Empleado::get_user_fecha_inactividad($_POST);
    $bonificacion = Empleado::get_user_bonificacion($_POST);
    $isr = Empleado::get_user_isr($_POST);


    if($nombre && $apellido && $estado && $fecha_inicio && $fecha_inactividad && $bonificacion){
        $nuevo = new Empleado();
        $nuevo->nombre = $nombre;
        $nuevo->apellido = $apellido;
        $nuevo->estado = $estado;
        $nuevo->fecha_inicio_labores = $fecha_inicio;
        $nuevo->fecha_inactividad = $fecha_inactividad;
        $nuevo->guardar();
        $basic->guardar_isr_y_bonos($nuevo, $fecha_inicio, $isr, $bonificacion);
        $basic->redireccionar('vista_empleados.php');
    } else {
        $basic->redireccionar("nuevo_empleado.php?m=revisar_datos");
    }
}

if(isset($_GET['m'])){
    echo
    "<script>
        alert('Por favor revise los datos ingresados');    
        document.location = 'nuevo_empleado.php';
    </script>";
}

?>
<div class="content">
    <form method="post" action="nuevo_empleado.php">
        <div class="combos2">
            <h3>Nuevo Empleado</h3>
            <p>Nombre: </p>
            <input class="alpha-only" type="text" name="nombre">
            <p>Apellido: </p>
            <input class="alpha-only"  type="text" name="apellido"></p>
            <p>Fecha Inicio de Labores:</p>
            <input class="mydate" type="date" name="fecha_inicio">
        </div>

        <div class="combos2">
            <p>Estado:</p>
            <span id="radiobutt">
                <input class="radioEstado" type="radio" name="estado" value="1" checked> Activo<br>
                <input class="radioEstado" type="radio" name="estado" value="2"> Inactivo<br>
            </span>
            <p>Fecha de Inactividad</p>
            <input disabled type="date" class="mydate" name="fecha_inactividad" id="radioFechaInactividad">
            <p>Bonificación: </p>
            Q. <input class="txtNum" type="text" name="bonificacion">
            <p>Cuota ISR: </p>
            Q. <input class="txtNum"  type="text" name="isr"><br><br>
            <input type="submit" name="newEmpleado" value="Guardar">
        </div>
    </form>

</div>

<?php
require_once("../Public/layouts/footer.php");
?>