<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 13/11/2016
 * Time: 11:27 AM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
$empleado = new Empleado();
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $empleado = Empleado::get_empleados($id);
    $historial_isr = "historial_isr.php?isr=false&id={$id}";

    if(isset($_POST['Guardar'])){
        $post = $_POST;
        $fecha = Isr::get_user_fecha_isr($post);
        $isr = Isr::get_user_isr($post);
        //$fecha = new DateTime($_POST['fecha']);
        //$bono = $_POST['bono'];
        $year_meses_con_isr = Isr::get_year_meses_con_isr($id);
        if($fecha && $isr){
            if(!Isr::year_mes_ya_existente($fecha, $year_meses_con_isr)){
                $isr = new Isr($empleado, $fecha, $isr);
                $isr->guardar();
                //$historial_isr = "historial_bonificaciones.php?bono=false&id={$id}";
                $basic->redireccionar($historial_isr);
            } else {
                $basic->redireccionar($historial_isr . "&m=mes_existente");
            }
        }else{
            $basic->redireccionar($historial_isr . "&m=revisar_datos");
        }
    }

} else {
    $basic->redireccionar('vista_empleados.php');
}



?>
<div class="content">
    <div class="bonos">
        <h3>Empleado: <?php echo $empleado->nombre_completo();?></h3>
        <h4>Ingrese Nuevo ISR:</h4>
        <form method="post" action="nuevo_isr.php?id=<?php echo $empleado->empleado_id; ?>">
            <p>Fecha: </p>
            <input type="date" class="mydate" name="fecha" value="<?php echo date('Y-m-d')?>">
            <p>ISR:</p>
            Q.<input class="txtNum" type="text" name="isr"><br><br>
            <input type="submit" name="Guardar" value="Guardar">
        </form>
    </div>
</div>

<?php
require_once("../Public/layouts/footer.php");
?>
