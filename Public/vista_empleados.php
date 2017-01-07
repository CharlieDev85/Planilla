<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 21/10/2016
 * Time: 3:08 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");

if(isset($_GET['m'])){
    echo
    "<script>
        alert('Por favor revise los datos ingresados');    
        document.location = 'vista_empleados.php';
    </script>";
}

if(isset($_GET['confirmar_eliminar'])){
    $id = intval($_GET['confirmar_eliminar']);
    $nombre_empleado = Empleado::get_empleados($id)->nombre_completo();
    echo
    "<script>

    var i = confirm('¿Desea borrar: {$nombre_empleado}?');
    if(i==true){
        document.location = 'vista_empleados.php?eliminar={$_GET['confirmar_eliminar']}';
    } else {
        document.location = 'vista_empleados.php';
    }
    
</script>";
}

if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    if($id){
        Empleado::eliminar($id);
    }
}

$empleados_tabla = Empleado::get_empleados_para_tabla();

?>
    <div class="content">
        <div class="combos1">
            <h3>Listado de Empleados</h3>
            <table class="responstable" >
                <tr>
                    <th width="8%">Código</th>
                    <th width="25%">Nombre Completo</th>
                    <th width="10%">Inicio</th>
                    <th width="7%">Estado</th>
                    <th width="10%">Inactividad</th>
                    <th width="11%">Bonificación</th>
                    <th width="9%">ISR</th>
                    <th width="20%">Acción</th>
                    <?php echo $empleados_tabla; ?>
                </tr>
            </table>
        </div>
        <br>


    </div>
<?php
require_once("../Public/layouts/footer.php");
?>