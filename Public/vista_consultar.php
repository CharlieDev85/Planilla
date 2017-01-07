<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 21/10/2016
 * Time: 3:09 PM
 */

require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
$tabla = "";
$year_selected = $_GET['year'];

if(isset($_GET['action']) && $_GET['action']=='confirmar_eliminar'){
    $mes_eliminar = $_GET['mes'];
    $confirm =
        "<script>
            var i = confirm('¿Desea borrar la planilla de {$basic->meses_default[$mes_eliminar]}, {$year_selected}?');
            if(i==true){
                document.location = 'vista_consultar.php?action=eliminar&year={$year_selected}&mes={$mes_eliminar}';
            } else {
                document.location = 'vista_consultar.php?action=false&year={$year_selected}&month={$mes_eliminar}';
            }    
        </script>";
     echo $confirm;
}

if(isset($_GET['action']) && $_GET['action']=='eliminar'){
    $month_selected = intval($_GET['mes']);
    Planilla::eliminar_planilla($year_selected, $month_selected);
    $url = "vista_consultar.php?year={$year_selected}";
    $basic->redireccionar($url);
}
if($year_selected != 'false'){
    $tabla = Planilla::get_tabla_meses_con_planilla($year_selected);
}
?>
<div class="content">
    <div class="combos1">
        <br>
        <h3>Consultar Planilla</h3>
        <p>Seleccione un año:</p>
        <form action="vista_consultar.php" method="get">
            <select name="year">
                <?php echo $basic->year_options($year_selected); ?>
            </select>
            <input type="submit" value="Buscar">
        </form>
        <hr>
        <?php echo $tabla; ?>
    </div>
</div>
<?php
require_once("../Public/layouts/footer.php");
?>
