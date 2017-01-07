<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 30/10/2016
 * Time: 6:57 PM
 */

require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");
global $basic;
$empleado = new Empleado();
$form = "";

if(isset($_GET['isr']) && isset($_GET['id'])){
    $isr_id = intval($_GET['isr']);
    $empleado_id = intval($_GET['id']);
    $empleado = Empleado::get_empleados($empleado_id);
    $tabla = Isr::get_isrs_tabla($empleado);

    if(isset($_GET['confirmar_eliminar_isr'])){
        $isr_id = intval($_GET['confirmar_eliminar_isr']);
        $isr_fecha = Isr::get_isrs($empleado, $isr_id)->isr_fecha;
        echo
        "<script>
            var i = confirm('¿Desea borrar el ISR del : {$isr_fecha}?');
            if(i==true){
                document.location = 'historial_isr.php?isr=false&id={$empleado_id}&eliminar_isr={$isr_id}';
            } else {
                document.location = 'historial_isr.php?isr=false&id={$empleado_id}';
            }    
        </script>";
    }

    if(isset($_GET['m'])){
        $m = $_GET['m'];
        $script_mes_existente = "<script> alert('Solamente se permite un ISR por mes'); document.location = 'historial_isr.php?isr=false&id={$empleado_id}'; </script>";
        $script_revisar_datos = "<script> alert('Por favor revisar datos'); document.location = 'nuevo_isr.php?id={$empleado_id}'; </script>";
        $script_revisar_datos_modificar_isr = "<script> alert('Por favor revisar datos'); document.location = 'historial_isr.php?isr=false&id={$empleado_id}'; </script>";
        switch($m){
            case "mes_existente":
                echo $script_mes_existente;
                break;
            case "revisar_datos":
                echo $script_revisar_datos;
                break;
            case "revisar_datos_modificar_isr":
                echo $script_revisar_datos_modificar_isr;
                break;
            default:
                $basic->redireccionar("historial_isr.php?isr=false&id={$empleado_id}&default=default");
        }
    }

    if(isset($_GET['eliminar_isr'])){
        $isr_id = intval($_GET['eliminar_isr']);
        Isr::eliminar($isr_id);
        $basic->redireccionar("historial_isr.php?isr=false&id={$empleado->empleado_id}");
    }

    if($_GET['isr'] != 'false'){
        $isr_id = intval($_GET['isr']);
        $isr_original = Isr::get_isrs($empleado, $isr_id);
        $form = $isr_original->get_form_editar();

        if(isset($_POST['modificar_isr'])){
            $post = $_POST;
            $nuevo_isr = Isr::get_user_isr($post);
            if($nuevo_isr){
                $nuevo_isr = new Isr($empleado, $isr_original->isr_fecha, $nuevo_isr);
                $nuevo_isr->editar($isr_original);
                $basic->redireccionar("historial_isr.php?isr=false&id={$nuevo_isr->empleado->empleado_id}");
            }else {
                $basic->redireccionar("historial_isr.php?isr=false&id={$empleado_id}&m=revisar_datos_modificar_isr");
            }
        }
    }

} else {
    $basic->redireccionar('vista_empleados.php');
}
?>

<div class="content">
    <div class="bonos">
        <h2>Historial de ISR</h2>
        <h3><b>Empleado: </b><?php echo $empleado->nombre_completo();?></h3>
        <a href="nuevo_isr.php?id=<?php echo $empleado_id;?>"> Agregar Nuevo ISR</a>
        <table class="table_bonos">
            <tr>
                <th>Fecha</th>
                <th>ISR</th>
                <th>Acción</th>
            </tr>
            <?php echo $tabla?>
        </table>
        <a href="javascript:history.back()"> &lt;&lt;  Regresar</a>
    </div>

    <div class="combos2" id="form_modificar_bono">
        <?php
        if($_GET['isr'] != 'false'){
            echo $form;
        }
        ?>


    </div>

</div>

<?php
require_once("../Public/layouts/footer.php");
?>
