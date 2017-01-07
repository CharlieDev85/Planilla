<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 1/11/2016
 * Time: 4:21 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
if(isset($_GET)){
    $year = $_GET['year'];
    $month_num = $_GET['mes'];
    $tabla = Planilla::get_tabla_planilla($year, $month_num);
}

?>
    <div class="content">
        <div class="combos1">
            <h3>Planilla: <?php echo $basic->meses_default[$month_num] . " " . $year;?> </h3>

                <table>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">Empleado</th>
                        <th width="10%">Inicio</th>
                        <th width="10%">Sueldo Ordinario</th>
                        <th width="10%">Bonificación</th>
                        <th width="10%">Total Sueldo</th>
                        <th width="10%">ISR</th>
                        <th width="10%">IGSS</th>
                        <th width="10%">Salario Líquido</th>
                        <th width="10%">Acción</th>
                    </tr>
            <?php echo $tabla;?>
                    <a href="javascript:history.back()"> &lt;&lt;  Regresar</a>
        </div>
    </div>
<?php
require_once("../Public/layouts/footer.php");
?>