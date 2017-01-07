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



if(isset($_GET['bono']) && isset($_GET['id'])){
    $bono_id = intval($_GET['bono']);
    $empleado_id = intval($_GET['id']);
    $empleado = Empleado::get_empleados($empleado_id);
    $tabla = Bonificacion::get_bonificaciones_tabla($empleado);

    if(isset($_GET['confirmar_eliminar_bono'])){
        $bono_id = intval($_GET['confirmar_eliminar_bono']);
        $bono_fecha = Bonificacion::get_bonificaciones($empleado, $bono_id)->bonificacion_fecha;
        echo
        "<script>
            var i = confirm('¿Desea borrar la bonificación del : {$bono_fecha}?');
            if(i==true){
                document.location = 'historial_bonificaciones.php?bono=false&id={$empleado_id}&eliminar_bono={$bono_id}';
            } else {
                document.location = 'historial_bonificaciones.php?bono=false&id={$empleado_id}';
            }    
        </script>";
    }

    if(isset($_GET['m'])){
        $m = $_GET['m'];
        $script_mes_existente = "<script> alert('Solamente se permite una bonificación por mes'); document.location = 'historial_bonificaciones.php?bono=false&id={$empleado_id}'; </script>";
        $script_revisar_datos = "<script> alert('Por favor revisar datos'); document.location = 'nueva_bonificacion.php?id={$empleado_id}'; </script>";
        $script_revisar_datos_modificar_bono = "<script> alert('Por favor revisar datos'); document.location = 'historial_bonificaciones.php?bono=false&id={$empleado_id}'; </script>";
        switch($m){
            case "mes_existente":
                echo $script_mes_existente;
                break;
            case "revisar_datos":
                echo $script_revisar_datos;
                break;
            case "revisar_datos_modificar_bono":
                echo $script_revisar_datos_modificar_bono;
                break;
            default:
                $basic->redireccionar("historial_bonificaciones.php?bono=false&id={$empleado_id}&default=default");
        }
    }

    if(isset($_GET['eliminar_bono'])){
        $bono_id = intval($_GET['eliminar_bono']);
        Bonificacion::eliminar($bono_id);
        $basic->redireccionar("historial_bonificaciones.php?bono=false&id={$empleado->empleado_id}");
    }

    if($_GET['bono'] != 'false'){
        $bono_id = intval($_GET['bono']);
        $bono_original = Bonificacion::get_bonificaciones($empleado, $bono_id);
        $form = $bono_original->get_form_editar();

        if(isset($_POST['modificar_bono'])){
            $post = $_POST;
            $nuevo_bono = Bonificacion::get_user_bono($post);
            if($nuevo_bono){
                $nueva_bonificacion = new Bonificacion($empleado, $bono_original->bonificacion_fecha, $nuevo_bono);
                $nueva_bonificacion->editar($bono_original);
                $basic->redireccionar("historial_bonificaciones.php?bono=false&id={$nueva_bonificacion->empleado->empleado_id}");
            }else {
                $basic->redireccionar("historial_bonificaciones.php?bono=false&id={$empleado_id}&m=revisar_datos_modificar_bono");
            }
        }
    }

} else {
    $basic->redireccionar('vista_empleados.php');
}
?>

<div class="content">
    <div class="bonos">
        <h2>Historial de Bonificaciones</h2>
        <h3><b>Empleado: </b><?php echo $empleado->nombre_completo();?></h3>
        <a href="nueva_bonificacion.php?id=<?php echo $empleado_id;?>"> Agregar Nueva Bonificación</a>
        <table class="table_bonos">
            <tr>
                <th>Fecha</th>
                <th>Bonificación</th>
                <th>Acción</th>
            </tr>
            <?php echo $tabla?>
        </table>
        <a href="javascript:history.back()"> &lt;&lt;  Regresar</a>
    </div>

    <div class="combos2" id="form_modificar_bono">
        <?php
        if($_GET['bono'] != 'false'){
            echo $form;
        }
        ?>


    </div>

</div>

<?php
require_once("../Public/layouts/footer.php");
?>
