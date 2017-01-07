<?php
/**
 * Created by PhpStorm.
 * User: carle
 * Date: 6/11/2016
 * Time: 2:06 PM
 */
require_once("../Includes/initialize.php");
require_once("../Public/layouts/header.php");
require_once("../Public/layouts/menu.php");

if(isset($_GET['confirmar_eliminar'])){
    echo
    "<script>

    var i = confirm('¿Desea borrar: {$_GET['confirmar_eliminar']}?');
    if(i==true){
        document.location = 'vista_igss_salario_ordinario.php?eliminar={$_GET['confirmar_eliminar']}';
    } else {
        document.location = 'vista_igss_salario_ordinario.php';
    }
    
</script>";
}

if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    if($id != 0){
        Igss_Salario::eliminar($id);
    }
}

$tabla_igss_salario = Igss_Salario::get_tabla();
?>
<div class="content">

    <h3>IGSS  -  Salario Ordinario</h3>
    <table class="tabla_igss_salario">
    <tr>
        <th width="25%">Año</th>
        <th width="25%">IGSS</th>
        <th width="25%">Salario Ordinario</th>
        <th width="25%">Acción</th>
    </tr>
        <?php
            echo $tabla_igss_salario;
        ?>
    </table>

</div>

<?php
require_once("../Public/layouts/footer.php");
?>
