<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 6/11/2016
 * Time: 4:22 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
if(isset($_GET['modificar'])){
    $year_modificar = intval($_GET['modificar']);
    if($year_modificar != 0){
        $igss_salario = Igss_Salario::get_object($year_modificar);
    }
} elseif (isset($_POST['guardar'])){
    $year = Igss_Salario::get_user($_POST['year']);
    $igss = Igss_Salario::get_user($_POST['igss']);
    $salario_ordinario = Igss_Salario::get_user($_POST['salario_ordinario']);
    if($year && $igss && $salario_ordinario){
        $igss_salario = new Igss_Salario($year);
        $igss_salario->cuota_igss = $igss;
        $igss_salario->salario_ordinario = $salario_ordinario;
        $igss_salario->actualizar();
    }
     $basic->redireccionar('vista_igss_salario_ordinario.php');
} else{
    $basic->redireccionar('vista_igss_salario_ordinario.php');
}
?>
<div class="content">
    <h3>Modificar Igss - Salario ordinario</h3>
    <h3>Año: <?php echo $igss_salario->year?></h3>
    <div class="combos1">
        <form method="post" action="modificar_igss_salario_ordinario.php">
            <p>Cuota IGSS:</p>
            <input type="hidden" name="year" value="<?php echo $igss_salario->year?>">
            Q.<input class="txtNum" type="text" name="igss" value="<?php echo $igss_salario->cuota_igss ?>">
            <p>Salario Ordinario:</p>
            Q.<input class="txtNum" type="text" name="salario_ordinario" value="<?php echo $igss_salario->salario_ordinario ?>"><br><br>
            <input type="submit" value="Guardar Cambios" name="guardar" >
        </form>
    </div>
</div>


<?php
require_once("../Public/layouts/footer.php");
?>