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
    $historial_bonos = "historial_bonificaciones.php?bono=false&id={$id}";

    if(isset($_POST['Guardar'])){
        $post = $_POST;
        $fecha = Bonificacion::get_user_fecha_bonificacion($post);
        $bono = Bonificacion::get_user_bono($post);
        //$fecha = new DateTime($_POST['fecha']);
        //$bono = $_POST['bono'];
        $year_meses_con_bonos = Bonificacion::get_year_meses_con_bonos($id);
        if($fecha && $bono){
            if(!Bonificacion::year_mes_ya_existente($fecha, $year_meses_con_bonos)){
                $bono = new Bonificacion($empleado, $fecha, $bono);
                $bono->guardar();
                //$historial_bonos = "historial_bonificaciones.php?bono=false&id={$id}";
                $basic->redireccionar($historial_bonos);
            } else {
                $basic->redireccionar($historial_bonos . "&m=mes_existente");
            }
        }else{
            $basic->redireccionar($historial_bonos . "&m=revisar_datos");
        }
    }

} else {
    $basic->redireccionar('vista_empleados.php');
}



?>
<div class="content">
    <div class="bonos">
        <h3>Empleado: <?php echo $empleado->nombre_completo();?></h3>
        <h4>Ingrese Nueva Bonificación:</h4>
        <form method="post" action="nueva_bonificacion.php?id=<?php echo $empleado->empleado_id; ?>">
            <p>Fecha: </p>
            <input type="date" class="mydate" name="fecha" value="<?php echo date('Y-m-d')?>">
            <p>Bonificación:</p>
            Q.<input class="txtNum" type="text" name="bono"><br><br>
            <input type="submit" name="Guardar" value="Guardar">
        </form>
    </div>
</div>

<?php
require_once("../Public/layouts/footer.php");
?>
