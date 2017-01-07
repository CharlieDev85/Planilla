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
$mensaje = "";
$year_selected = $_GET['year']=='false'? false : intval($_GET['year']);
$month_selected = $_GET['month']=='false'? false : intval($_GET['month']);
$tabla_generar_planilla = "";
$planilla = new Planilla();

if(isset($_GET['m'])){
    $m = $_GET['m'];
    $msj_no_data =
        "<script>
            alert('No hay datos suficientes para el mes y año seleccionados');
            document.location = 'vista_generar.php?action=false&year=false&month=false';
        </script>";
    $msj_ya_existente =
        "<script>
            alert('La planilla del mes y año seleccionados ya fue guardada con anterioridad');
            document.location = 'vista_generar.php?action=false&year=false&month=false';
        </script>";
    $default =
        "<script>
            document.location = 'vista_generar.php?action=false&year=false&month=false';
        </script>";
    $msj_guardado_ok =
        "<script>
            alert('Planilla guardada correctamente');
            document.location = 'vista_generar.php?action=false&year=false&month=false';
        </script>";

    switch($m){
        case "no_data":
            echo $msj_no_data;
            break;
        case "ya_existente":
            echo $msj_ya_existente;
            break;
        case "guardado_ok":
            echo $msj_guardado_ok;
            break;
        default:
            echo $default;
    }
}

if(isset($_GET['generar'])){
    $get = $_GET;
    $year = Planilla_Empleado::get_selected_year($get);
    $month = Planilla_Empleado::get_selected_month($get);
    $datos_de_planilla = Planilla_Empleado::get_tabla_y_planilla($year, $month);
    if($datos_de_planilla){
        $tabla_generar_planilla = $datos_de_planilla['tabla'];
        $planilla->planilla = $datos_de_planilla['planilla'];
    } else {
        $basic->redireccionar("vista_generar.php?action=false&year=false&month=false&m=no_data");
    }

}

if(isset($_POST['guardar_planilla'])){
    $planilla_ya_existente = $planilla->ya_existente();
    if($planilla_ya_existente){
        $basic->redireccionar("vista_generar.php?action=false&year=false&month=false&m=ya_existente");
    }
    $planilla->guardar();
    $basic->redireccionar("vista_generar.php?action=false&year=false&month=false&m=guardado_ok");
}

?>
    <div class="content">
        <div class="generar1">
            <h3>Generar Planilla</h3>
            <form method="get" action="vista_generar.php">
                <div class="generar2">
                    <p>Seleccione un año.</p>
                    <select name="year">
                        <?php echo $basic->year_options($year_selected); ?>
                    </select>
                </div>
                <div class="generar2">
                    <p>Seleccione un mes:</p>
                    <select name="month">
                        <?php echo $basic->month_options($month_selected); ?>
                    </select>
                </div>
                <input class="search" type="submit" value="Generar" name="generar">
            </form>
        </div>
        <div class="generar_planilla">
            <?php echo $mensaje;?>
            <?php echo $tabla_generar_planilla; ?>
        </div>
    </div>
<?php
require_once("../Public/layouts/footer.php");
?>