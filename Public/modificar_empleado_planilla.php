<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 1/11/2016
 * Time: 5:39 PM
 */

require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
$id = intval($_GET['id']);
$empleado_planilla = Planilla::get_planilla_empleado_para_editar($id);
if(isset($_POST['modificar_planilla_empleado'])){
    $post = $_POST;
    $nuevo_sueldo_ordinario = Planilla::get_user_sueldo_ordinario($post);
    $nuevo_bonificacion     = Planilla::get_user_bonificacion($post);
    $nuevo_isr              = Planilla::get_user_isr($post);
    $nuevo_igss             = Planilla::get_user_igss($post);
    if($nuevo_sueldo_ordinario && $nuevo_bonificacion && $nuevo_isr && $nuevo_igss){
        $nuevo_planilla_empleado = new Planilla_Empleado();
        $nuevo_planilla_empleado->planilla_empleado_id  = $id;
        $nuevo_planilla_empleado->sueldo_ordinario      = $nuevo_sueldo_ordinario;
        $nuevo_planilla_empleado->bonificacion          = $nuevo_bonificacion;
        $nuevo_planilla_empleado->isr                   = $nuevo_isr;
        $nuevo_planilla_empleado->igss                  = $nuevo_igss;
        $nuevo_planilla_empleado->modificar();
        $url = "mostrar_planilla.php?year={$empleado_planilla->year}&mes={$empleado_planilla->mes}";
        $basic->redireccionar($url);
    } else {
        $basic->redireccionar("vista_consultar.php?action=false&year=false&month=false");
    }

}
?>
<div class="content">
    <h3>Modificar Planilla de: <?php echo $empleado_planilla->empleado->nombre_completo(); ?></h3>
    <div class="combos1">
        <h4>Para: <?php echo $basic->meses_default[$empleado_planilla->mes] . ' '. $empleado_planilla->year ?></h4>
        <form method="post" action="modificar_empleado_planilla.php?id=<?php echo $id;?>">
            <p>Sueldo Ordinario:</p>
            <input type="text" class="txtNum" name="sueldo_ordinario" value="<?php echo $empleado_planilla->sueldo_ordinario; ?>">
            <p>Bonificación</p>
            <input type="text"  class="txtNum" name="bonificacion" value="<?php echo $empleado_planilla->bonificacion; ?>">
            <p>Retención ISR</p>
            <input type="text" class="txtNum" name="isr" value="<?php echo $empleado_planilla->isr; ?>">
            <p>Cuota IGSS</p>
            <input type="text" class="txtNum" name="igss" value="<?php echo $empleado_planilla->igss?>">
            <br><br>
            <input type="submit" name="modificar_planilla_empleado" value="Guardar Cambios">
        </form>
    </div>
</div>

<?php
require_once("../Public/layouts/footer.php");
?>


