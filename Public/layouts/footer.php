</div><?php //cierra div wrapper?>

<div class="footer">Creado por: Carlos Marroquin</div>
</div><?php //cierra div container?>
	</body>

</html>
<?php
  //Close database connection
	global $db;
	if(isset($db)) { $db->close_connection(); }
?>
