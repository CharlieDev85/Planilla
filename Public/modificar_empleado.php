<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 30/10/2016
 * Time: 4:11 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
if(isset($_GET['id'])){
    $emp_id = intval($_GET['id']);
    $original = Empleado::get_empleados($emp_id);

//    var_dump($_POST);
    if(isset($_POST['update_empleado'])){
        $post = $_POST;
        $nombre = Empleado::get_user_nombre($post);
        $apellido = Empleado::get_user_apellido($post);
        $fecha_inicio = Empleado::get_user_fecha_inicio($post);
        $estado = Empleado::get_user_estado($post);
        $fecha_inactividad = Empleado::get_user_fecha_inactividad($post);

        if($nombre && $apellido && $estado && $fecha_inicio && $fecha_inactividad ){
            $nuevo = new Empleado();
            $nuevo->nombre = $nombre;
            $nuevo->apellido = $apellido;
            $nuevo->estado = $estado;
            $nuevo->fecha_inicio_labores = $fecha_inicio;
            $nuevo->fecha_inactividad = $fecha_inactividad;
            $nuevo->editar($original);
//            $original = $nuevo;
            $basic->redireccionar('vista_empleados.php');
        } else {
            $basic->redireccionar("vista_empleados.php?m=revisar_datos");
        }

    }
//    var_dump($original);
    $activo = $original->estado == 'Activo' || $original->estado == 1? true: false;
} else {
    $basic->redireccionar('vista_empleados.php');
}


?>

<div class="content">
    <form method="post" action="modificar_empleado.php?id=<?php echo $emp_id;?>">
        <div class="combos2">
            <h3>Modificar Empleado</h3><br><br>
            <p>Nombre: </p>
            <input class="alpha-only" type="text" name="nombre" value="<?php echo $original->nombre ?>">
            <p>Apellido: </p>
            <input class="alpha-only"  type="text" name="apellido" value="<?php echo $original->apellido ?>"></p>
            <p>Fecha Inicio de Labores:</p>
            <input type="date" class="mydate" name="fecha_inicio" value="<?php echo $original->fecha_inicio_labores ?>">
            <p>Estado:</p>
            <span id="radiobutt">
                <input class="radioEstado" type="radio" name="estado" value="1"<?php if($activo){echo 'checked';}?>> Activo<br>
                <input class="radioEstado" type="radio" name="estado" value="2"<?php if(!$activo){echo 'checked';}?>> Inactivo<br>
            </span>
            <p>Fecha de Inactividad</p>
            <input id="radioFechaInactividad" type="date" class="mydate" name="fecha_inactividad" value="<?php if(!$activo){echo $original->fecha_inactividad;}?>" <?php if($activo){echo "disabled";}?>>
            <br><br>
            <input type="submit" name="update_empleado" value="Guardar Cambios">
        </div>

        <div class="combos2">

            <br><br><br>
            <a href="historial_bonificaciones.php?bono=false&id=<?php echo $original->empleado_id; ?>">Historial de Bonificaciones</a><br><br>
            <a href="historial_isr.php?isr=false&id=<?php echo $original->empleado_id; ?>">Historial de Cuotas ISR</a><br><br><br>

        </div>
    </form>

</div>

<?php
require_once("../Public/layouts/footer.php");
?>
