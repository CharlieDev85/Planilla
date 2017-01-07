<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 6/11/2016
 * Time: 5:13 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
$error = array('year'=>'');
if(isset($_POST['Guardar'])){
    $year = intval(Igss_Salario::get_user($_POST['year']));
    $igss = Igss_Salario::get_user($_POST['igss']);
    $salario_ordinario = Igss_Salario::get_user($_POST['salario_ordinario']);
    if($year && $igss && $salario_ordinario){
        $igss_salario = new Igss_Salario($year);
        $igss_salario->cuota_igss = $igss;
        $igss_salario->salario_ordinario = $salario_ordinario;
        $ok = $igss_salario->save();
        if($ok){
            $basic->redireccionar('vista_igss_salario_ordinario.php');
        } else {
            $error['year'] = 'Año '. $year .' ya fue ingresado en la Base de Datos';
        }

    }
}
?>

<div class="content">
    <div class="combos1">
        <h3>Nuevo Igss  -  Salario Ordinario</h3>
        <form method="post" action="">
            <?php if($error['year']!=''){echo '<p class="error">' . $error['year'] . '</p>';}?>
            <p>Año</p>
            <input class="txtNum" type="number" name="year" min="2015" max="2030">
            <p>Cuota de IGSS</p>
            Q.<input class="txtNum" type="text" name="igss">
            <p>Salario Ordinario</p>
            Q.<input class="txtNum" type="text" name="salario_ordinario"><br><br>
            <input type="submit" name="Guardar" value="Guardar">
        </form>
    </div>
</div>


<?php
require_once("../Public/layouts/footer.php");
?>